"""
Simple Weather Predictive Model using Random Forest Regressor
"""

import numpy as np
import pandas as pd
import matplotlib
matplotlib.use('Agg')  # Non-interactive backend
import matplotlib.pyplot as plt
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score


def generate_synthetic_weather_data(n_samples=1000, random_state=42):
    """Generate synthetic weather data for demonstration purposes."""
    np.random.seed(random_state)

    # Features
    humidity = np.random.uniform(20, 100, n_samples)          # %
    pressure = np.random.uniform(980, 1030, n_samples)        # hPa
    wind_speed = np.random.uniform(0, 30, n_samples)          # km/h
    precipitation = np.random.uniform(0, 50, n_samples)       # mm
    cloud_cover = np.random.uniform(0, 100, n_samples)        # %
    month = np.random.randint(1, 13, n_samples)               # 1-12

    # Target: Temperature (Celsius) - synthetic relationship with some noise
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


def train_weather_model(data):
    """Train a Random Forest Regressor to predict temperature."""
    # Features and target
    X = data.drop('temperature', axis=1)
    y = data['temperature']

    # Train-test split
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.2, random_state=42
    )

    # Model
    model = RandomForestRegressor(n_estimators=100, random_state=42)
    model.fit(X_train, y_train)

    # Predictions
    y_pred = model.predict(X_test)

    # Metrics
    mae = mean_absolute_error(y_test, y_pred)
    rmse = np.sqrt(mean_squared_error(y_test, y_pred))
    r2 = r2_score(y_test, y_pred)

    print("=" * 50)
    print("Weather Prediction Model - Performance Metrics")
    print("=" * 50)
    print(f"Mean Absolute Error (MAE): {mae:.2f} C")
    print(f"Root Mean Squared Error (RMSE): {rmse:.2f} C")
    print(f"R2 Score: {r2:.4f}")
    print("=" * 50)

    # Feature Importance
    feature_importance = pd.DataFrame({
        'feature': X.columns,
        'importance': model.feature_importances_
    }).sort_values('importance', ascending=False)

    print("\nFeature Importance:")
    print(feature_importance.to_string(index=False))

    return model, X_test, y_test, y_pred, feature_importance


def plot_results(y_test, y_pred):
    """Plot actual vs predicted temperatures."""
    plt.figure(figsize=(8, 6))
    plt.scatter(y_test, y_pred, alpha=0.6, edgecolors='k')
    plt.plot([y_test.min(), y_test.max()], [y_test.min(), y_test.max()], 'r--', lw=2)
    plt.xlabel('Actual Temperature (C)')
    plt.ylabel('Predicted Temperature (C)')
    plt.title('Actual vs Predicted Temperature')
    plt.grid(True, alpha=0.3)
    plt.tight_layout()
    plt.savefig('weather_prediction_results.png')
    print("\nPlot saved as 'weather_prediction_results.png'")


def predict_sample(model, sample_data):
    """Make a prediction on a single sample."""
    prediction = model.predict([sample_data])
    return prediction[0]


def main():
    print("Generating synthetic weather data...")
    data = generate_synthetic_weather_data(n_samples=1000)
    print(f"Dataset shape: {data.shape}")
    print("\nFirst 5 rows of the dataset:")
    print(data.head())

    print("\nTraining the weather prediction model...")
    model, X_test, y_test, y_pred, feature_importance = train_weather_model(data)

    # Plot results
    plot_results(y_test, y_pred)

    # Sample prediction
    sample = {
        'humidity': 65,
        'pressure': 1013,
        'wind_speed': 10,
        'precipitation': 5,
        'cloud_cover': 40,
        'month': 6
    }
    sample_df = pd.DataFrame([sample])
    predicted_temp = model.predict(sample_df)[0]
    print("\nSample Prediction:")
    print(f"Input: {sample}")
    print(f"Predicted Temperature: {predicted_temp:.2f} C")


if __name__ == "__main__":
    main()

