import urllib.request
import json

# Test predict endpoint
data = json.dumps({
    'humidity': 65,
    'pressure': 1013,
    'wind_speed': 10,
    'precipitation': 5,
    'cloud_cover': 40,
    'month': 6
}).encode()

req = urllib.request.Request(
    'http://127.0.0.1:5000/predict',
    data=data,
    headers={'Content-Type': 'application/json'}
)

response = urllib.request.urlopen(req)
print("Predict Response:")
print(response.read().decode())

# Test fetch-weather endpoint
response2 = urllib.request.urlopen('http://127.0.0.1:5000/fetch-weather?location=London')
print("\nFetch Weather Response:")
print(response2.read().decode())
