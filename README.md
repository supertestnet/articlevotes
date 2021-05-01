Zip the two php files into one zip archive to create a wordpress plugin. Upload that wordpress plugin to a wordpress instance to install it. The file LightningController is also interesting on its own because it offers easy ways to control an LNBits instance using php. See documentation.md for more info.

Enter your lnbits credentials in Dashboard > Settings > Article Votes. Then you can connect this plugin to any publicly accessible LNBits instance except ones based on tor. (You can support tor-based LNBits instances by teaching your server how to "talk" to the tor network but that is out of scope for these instructions. 

Example code for displaying the articles and adding more of them is provided in the files display.html, admin.html, and add.html. To make the files I copy/pasted them from a dummy wordpress site I made while working on the plugin. Then I wrapped them in basic html tags and stuck them in "body":

```
<html>
<head>
</head>
<body>
</body>
</html>
```

I recommend that if you are using wordpress, do the reverse of what I did to make them: create three new pages in wordpress, and for each one, copy the html between the "body" tags and paste the contents into your wordpress pages. Manage the articles from admin.html which will probably end up as something like mywordpresswebsite.com/admin/ once you create a page for it.
