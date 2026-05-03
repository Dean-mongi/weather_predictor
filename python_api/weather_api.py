"""
Weather Prediction API Service
Flask-based API that exposes the Random Forest weather prediction model.
"""

import os
import json
import pickle
import numpy as np
import pandas as pd
from flask import Flask, request, jsonify
from flask_cors import CORS
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score

app = Flask(__name__)
CORS(app)

MODEL_PATH = os.path.join(os.path.dirname(__file__), 'weather_model_runtime.pkl')
model_cache = None


def generate_synthetic_weather_data(n_samples=1200, random_state=42):
    """Generate synthetic weather data for training."""
    np.random.seed(random_state)
    humidity = np.random.uniform(20, 100, n_samples)
    pressure = np.random.uniform(980, 1030, n_samples)
    wind_speed = np.random.uniform(0, 30, n_samples)
    precipitation = np.random.uniform(0, 50, n_samples)
    cloud_cover = np.random.uniform(0, 100, n_samples)
    month = np.random.randint(1, 13, n_samples)

    base_temp = 15 + 10 * np.sin(2 * np.pi * month / 12)
    temp = (
        base_temp
        - 0.1 * humidity
        + 0.05 * pressure
        - 0.3 * wind_speed
        - 0.2 * precipitation
        - 0.05 * cloud_cover
        + np.random.normal(0, 3, n_samples)
    )

    data = pd.DataFrame({
        'humidity': humidity,
        'pressure': pressure,
        'wind_speed': wind_speed,
        'precipitation': precipitation,
        'cloud_cover': cloud_cover,
        'month': month,
        'temperature': temp
    })
    return data


def train_and_save_model():
    """Train the model and save it to disk."""
    data = generate_synthetic_weather_data(n_samples=2000)
    X = data.drop('temperature', axis=1)
    y = data['temperature']

    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.2, random_state=42
    )

    model = RandomForestRegressor(n_estimators=60, random_state=42, n_jobs=1)
    model.fit(X_train, y_train)

    y_pred = model.predict(X_test)
    metrics = {
        'mae': float(mean_absolute_error(y_test, y_pred)),
        'rmse': float(np.sqrt(mean_squared_error(y_test, y_pred))),
        'r2': float(r2_score(y_test, y_pred))
    }

    feature_importance = pd.DataFrame({
        'feature': X.columns,
        'importance': model.feature_importances_
    }).sort_values('importance', ascending=False)

    with open(MODEL_PATH, 'wb') as f:
        pickle.dump(model, f)

    return metrics, feature_importance.to_dict('records')


def load_model():
    """Load the trained model from disk."""
    if not os.path.exists(MODEL_PATH):
        return None
    with open(MODEL_PATH, 'rb') as f:
        return pickle.load(f)


def get_or_create_model():
    """Get existing model or train a new one."""
    global model_cache

    if model_cache is not None:
        return model_cache

    model = load_model()
    if model is None:
        train_and_save_model()
        model = load_model()

    model_cache = model
    return model


def simulate_weather_for_location(location, month=None):
    """Simulate current weather conditions for a given location."""
    np.random.seed(hash(location) % 2**32)
    if month is None:
        month = pd.Timestamp.now().month

    return {
        'location': location,
        'humidity': float(np.random.uniform(30, 90)),
        'pressure': float(np.random.uniform(990, 1025)),
        'wind_speed': float(np.random.uniform(0, 25)),
        'precipitation': float(np.random.uniform(0, 30)),
        'cloud_cover': float(np.random.uniform(0, 100)),
        'month': int(month)
    }


@app.route('/')
def index():
    return jsonify({
        'service': 'Weather Prediction API',
        'version': '1.0.0',
        'endpoints': {
            'predict': 'POST /predict - Predict temperature from weather features',
            'fetch': 'GET /fetch-weather?location=<city> - Fetch simulated weather for location',
            'train': 'POST /train - Retrain the model',
            'health': 'GET /health - API health check'
        }
    })


@app.route('/health', methods=['GET'])
def health():
    model = get_or_create_model()
    return jsonify({
        'status': 'healthy',
        'model_loaded': model is not None
    })


@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json()
        required_fields = ['humidity', 'pressure', 'wind_speed', 'precipitation', 'cloud_cover', 'month']

        for field in required_fields:
            if field not in data:
                return jsonify({'error': f'Missing field: {field}'}), 400

        model = get_or_create_model()
        input_df = pd.DataFrame([{
            'humidity': float(data['humidity']),
            'pressure': float(data['pressure']),
            'wind_speed': float(data['wind_speed']),
            'precipitation': float(data['precipitation']),
            'cloud_cover': float(data['cloud_cover']),
            'month': int(data['month'])
        }])

        prediction = model.predict(input_df)[0]

        return jsonify({
            'predicted_temperature': round(float(prediction), 2),
            'unit': 'celsius',
            'input_features': input_df.to_dict('records')[0]
        })

    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/fetch-weather', methods=['GET'])
def fetch_weather():
    location = request.args.get('location', 'Unknown')
    month = request.args.get('month', type=int)

    weather = simulate_weather_for_location(location, month)

    model = get_or_create_model()
    input_df = pd.DataFrame([{
        'humidity': weather['humidity'],
        'pressure': weather['pressure'],
        'wind_speed': weather['wind_speed'],
        'precipitation': weather['precipitation'],
        'cloud_cover': weather['cloud_cover'],
        'month': weather['month']
    }])

    prediction = model.predict(input_df)[0]
    weather['predicted_temperature'] = round(float(prediction), 2)

    return jsonify(weather)


@app.route('/train', methods=['POST'])
def train():
    try:
        metrics, feature_importance = train_and_save_model()
        return jsonify({
            'message': 'Model trained successfully',
            'metrics': metrics,
            'feature_importance': feature_importance
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500


if __name__ == '__main__':
    print("Starting Weather Prediction API...")
    get_or_create_model()
    print("Model loaded. API ready.")
    app.run(host='127.0.0.1', port=5000, debug=False)

