import json

with open('optimized.json') as file:
    data = json.load(file)


categories  = []

for item in data:
    if item['category'] not in categories:
        categories.append(item['category'])

for category in categories:
    print(category)