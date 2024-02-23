import json
import requests
import eventlet
from bs4 import BeautifulSoup


categories = [
    'processors',
    'motherboards',
    'memory',
    'graphic-card',
    'ssd',
    'hard-disks',
    'fans-pc-cooling',
    'power-supply',
    'cases',
    'monitors',
    'keyboard-mouse',
    'headphones-speakers'
]

productsArr = []

for category in categories:
    print(f"Extracting {category}...")
    URL = f"https://maximumhardware.store/{category}?limit=100"
    html = requests.get(URL)
    soup = BeautifulSoup(html.content,features='html.parser')
    products = soup.find_all('div',class_='product-layout')

    for product in products:
        newProduct = {}
        container = product.find('div',class_='product-thumb')

        #text information
        caption = container.find('div',class_='caption')
        #name
        nameContainer = caption.find('div',class_='name')
        nameATag = nameContainer.find('a')
        name = nameATag.text
        newProduct['title'] = name

        #Prevent duplicates
        shouldBreak = False
        for item in productsArr:
             if newProduct['title'] == item['title']:
                  shouldBreak = True
        
        if shouldBreak :
             print("Breaking..bad")
             continue


        #price
        priceContainer = caption.find('div',class_='price')
        priceSpan = priceContainer.find('span',class_='price-normal')
        price = priceSpan.text if priceSpan else None
        newProduct['price'] = price




        # Image
        print(f"Extracting {category}'s images....")

        imageContainer = container.find('div',class_='image-group')
        imageTagContainer = imageContainer.find('div',class_='image')
        productHref = imageContainer.find('a',class_='product-img')['href']
        
        htmlPage = requests.get(productHref)
        pageSoup = BeautifulSoup(htmlPage.content,features='html.parser')
        swiperWrapper = pageSoup.find('div',class_='swiper-wrapper')
        swiperSlides = swiperWrapper.find_all('div',class_='swiper-slide')
        imgsArr = []
        for slide in swiperSlides:
                img = slide.find('img')['src']
                imgsArr.append(img)

        
        newProduct['images'] = imgsArr
        newProduct['category'] = category

        
        productsArr.append(newProduct)
        with open('data.json','w') as json_file:
            json.dump(productsArr,json_file,indent=4)

    print(f"{category} Extracted successfully\n")



with open('data.json','w') as json_file:
    json.dump(productsArr,json_file,indent=4)
