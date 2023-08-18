# Easy CMS
 Add a CMS to your custom HTML and PHP website to easily change and/or update content.

Easy CMS is made to easily make you able to change content on the fly, without having to go into the HTML or PHP files, or to give customers easy access to change their content.

*NOTE: It is not made to create a blog type of website, it is purely meant to get easy access to editing your content.*

##How to use
Easy CMS uses basic HTML tags as a base, you make a div and add components to that.

The available tags are:
- `<h#>` All header tags from h1 to h6 are useable.
- `<p>` The paragraph tag gives you a place to enter multiple lines of text. (formatting is still WIP)
- `<a>` The link tag gives the possibility to edit the shown text and the href link.
- `<img>` The image tag lets you input a (local) url to a image and displays it.

Easy CMS looks through all your php and HTML files and looks for the CMS-*yourID*  tag and grabs the above tags that are available.

For Example, you add the following to your index.html:
```
<div id="CMS-index1">
	<h1>heading</h1>
	<p>paragraph</p>
	<a href="#">link</a>
	<img src="#"></img>
</div>
```

Easy CMS grabs the CMS-*yourID* id tag and looks for which tags are inside it, at this moment, all 4 tags are inside it so it shows the input fields for all of them in the interface:

![](https://i.imgur.com/VhBXsh8.jpg)

you can change the values of the fields, press "submit" and it will change the value of the fields you edited.


------------



**!! You can only use 1 of each type of tag in every div !!**

So you can use: 
- 1 header `<h#>`
- 1 paragraph `<p>`
- 1 link `<a>` 
- 1 image `<img>`

Per div, but you can add a endless amount of divs.

## How to install
- Copy **cms.php** and the **cms-files** folder to your main directory.
- go to your mysql database server and import the **EASYCMS_Database.sql** file.
- go to the **cms-files** directory and open **cms_config.php**.
- Change the database settings to your database.
- edit the username and password for login.
- save the **cms_config.php** file.
- go to: **https://yourdomain.com/cms.php** and login.



### Disclaimer
Although there is a login system and i am reasonably sure it is safe, i am **NOT** a Cybersecurity expert and i coding is a just a hobby of mine, not my professional job.

**Usage is at your own risk!**
I am not liable in anyway.
