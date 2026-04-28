# Simple Weather Predictive Model

A simple weather prediction model built with Python and scikit-learn that predicts temperature based on humidity, pressure, wind speed, precipitation, cloud cover, and month.

## Features

- Generates synthetic weather data for demonstration
- Uses Random Forest Regressor for temperature prediction
- Evaluates model performance with MAE, RMSE, and R² metrics
- Visualizes actual vs predicted temperatures
- Provides feature importance analysis

## Requirements

- Python 3.7+
- numpy
- pandas
- scikit-learn
- matplotlib

## Installation

```bash
pip install -r requirements.txt
```

## Usage

Run the main script:

```bash
python weather_predictor.py
```

The script will:
1. Generate synthetic weather data
2. Train a Random Forest model
3. Display performance metrics
4. Show feature importance
5. Save a plot of actual vs predicted temperatures
6. Make a sample prediction

## Model Details

- **Algorithm**: Random Forest Regressor (100 estimators)
- **Target Variable**: Temperature (Celsius)
- **Input Features**:
  - Humidity (%)
  - Pressure (hPa)
  - Wind Speed (km/h)
  - Precipitation (mm)
  - Cloud Cover (%)
  - Month (1-12)

## Output

The script outputs:
- Mean Absolute Error (MAE)
- Root Mean Squared Error (RMSE)
- R² Score
- Feature Importance ranking
- Sample prediction for given input
- Visualization saved as `weather_prediction_results.png`

