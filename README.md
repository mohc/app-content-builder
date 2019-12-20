<p align="center"><a href="https://www.sellfino.com" target="_blank" rel="noopener noreferrer"><img width="200" src="https://www.sellfino.com/images/logo.png" alt="Sellfino logo"></a></p>

---

## Content Builder
**Custom sections everywhere, custom fields everywhere.**

Open element (product, page, article, customer, etc.) in the app and you can edit content like *Wordpress ACF, Statamic or Craft*.

Content Builder uses fieldsets - which are schema on steroids, because you don't have to code them by hand - drag and drop them! 
You want to use product fieldset on this one article you need - no problem. Fieldset for the collections on one of the pages - who cares, NO PROBLEM! 

**Every item** in the store, product, collection, page, article, blog, customer, order, even store - can use every fieldset-schema you create! Sections available everywhere without hacks and tricks - content is saved in metafields so you have access to it from every part of the website!

## Demo & Screenshots
Check how this app works in the live store: [DEMO](https://sellfino.myshopify.com/pages/content-builder-sample-page)

<a href="https://sellfino.com/images/screens/scb/scb-1.jpg" target="_blank" rel="noopener noreferrer"><img width="24%" src="https://sellfino.com/images/screens/scb/scb-1a.jpg"></a> <a href="https://sellfino.com/images/screens/scb/scb-2.jpg" target="_blank" rel="noopener noreferrer"><img width="24%" src="https://sellfino.com/images/screens/scb/scb-2.jpg"></a> <a href="https://sellfino.com/images/screens/scb/scb-3.jpg" target="_blank" rel="noopener noreferrer"><img width="24%" src="https://sellfino.com/images/screens/scb/scb-3.jpg"></a> <a href="https://sellfino.com/images/screens/scb/scb-4.jpg" target="_blank" rel="noopener noreferrer"><img width="24%" src="https://sellfino.com/images/screens/scb/scb-4a.jpg"></a> <a href="https://sellfino.com/images/screens/scb/scb-5.jpg" target="_blank" rel="noopener noreferrer"><img width="24%" src="https://sellfino.com/images/screens/scb/scb-5.jpg"></a> <a href="https://sellfino.com/images/screens/scb/scb-6.jpg" target="_blank" rel="noopener noreferrer"><img width="24%" src="https://sellfino.com/images/screens/scb/scb-6.jpg"></a> 

## Installation
- **1.** Add this app to Sellfino App Store.
- **2.** Add your first fieldset first. For each field ID is required and has to be unique through out the whole schema. Fieldsets are saved in `ContentBuilder.json` file, so you can export/import them with just copy-paste.
- **3.** Go back to index page of the app and choose the element you want to edit.
- **4.** Choose the fieldset and save it. Whole content, with fieldset id too, is saved in element's metafields (*scb* namespace). Below is the information for specific fields and how to use them in liquid files.

## Fields
Although content builder can be used for all types of content in your Shopify store (product, collection, customer, etc) - for making it as simple to explain, we will show to use fields based on the metafields of the simple page.

*fieldId* is the ID you set for the field - this is just the example.

---
### Text / Textarea / Richtext / Select
*string*
```
{{ page.metafields.scb.fieldId }}
```
---
### Links
*array*
```
{% for link in page.metafields.scb.fieldId %}
  {{ link.type }} , {{ link.value }} , {{ link.id }}
{% endfor %}
```

**type** - type of the link (product, collection, custom url)

**value** - string value. URL returns full as text, other - handles to elements

**id** - id of the element

---
### Gallery
*array*
```
{% for file in page.metafields.scb.fieldId %}
  {{ file.url }} , {{ file.title }}
{% endfor %}
```

Gallery can be used for all the files, not only the images. Files are saved in additional theme, created automatically when you activate Content Builder.

#### Image transformations
As mentioned, files are saved in additional theme, so it's not possible to use `img_url` on the uploaded images. However, Shopify can do the transformations by changing the URL of the image with extra suffixes. To make it much simiplier, you can use the snippet, added to this repository, called `img_url.liquid`:
- **1.** Add `img_url.liquid` to your theme under *Snippets* folder
- **2.** In the image tag, include the snippet like below:
```
<img src="{% include 'img_url' with url: fieldId[0].url, params: '160x' %}" />
```
**url** - URL of the image

**params** - parameters of transformation. Few examples what can be used as parameter:
```
160x, 300x300, x50, 150x150_crop_center
```
---
### Sections
*array*

The cleanest way to handle sections is to put every section into the individual snippet. If you prefix all the snippets with *scb-*, you can use the code below to include the sections:
```
{% for block in page.metafields.scb.fieldId %}
  {% assign data = block.value %}
	{% assign section_name = 'scb-' | append: block.type %}
	{% include section_name with data %}
{% endfor %}
```
Then, with `data` variable, you can access fields for the specific block (*data.fieldId*)

---
## Sellfino Open Source Shopify App Store
This is the app for [Sellfino](https://github.com/sellfino/sellfino) platform.

#### Support and Contribution

Join our awesome community! Here is how you can connect with us:
- [Website](https://www.sellfino.com) - all info here + live chat
- [Discord](https://discordapp.com/invite/wrFnzZ3) - channels to discuss new ideas and ask for help
- [Messanger](https://m.me/104484064333760) - if you want to chat on Facebook
- [Email](mailto:contact@sellfino.com) - whenever we are out of touch, drop us a message


## Copyright
Copyright (c) 2019-present, Lucas Szarzynski
