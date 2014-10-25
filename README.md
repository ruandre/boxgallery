# BoxGallery

A little WordPress shortcode plugin for displaying multiple lightbox galleries per post or page, each represented by a single image. Created in response to this post: http://plus.google.com/117052107038197839303/posts/1HCY5QckhKu

### How it works:

This plugin's shortcode works just like the default WordPress gallery shortcode.

In fact, the easiest way to use it is to add a gallery like you normally would and simply rename it afterwards.

### How to use it:

Download, install and activate this plugin.

Then, on the post or page edit screen:

1. Click the **Add Media** button.
2. Next, click **Create Gallery** to the left.
3. Select some images and click **Create a new gallery**.
4. Finally, click **Insert gallery**.

You'll end up with something like this:

```
[gallery ids="11,8,7"]
```

Just rename it to **boxgallery** and you're set:

```
[boxgallery ids="11,8,7"]
```

You can specify your own **thumbnail image id** like so:

```
[boxgallery ids="8,7" thumb="11"]
```

You can also specify an image **size** like so:

```
[boxgallery ids="11,8,7" size="thumbnail"]
```

### Credits:

[Lightbox by Lokesh Dhakar](http://github.com/lokesh/lightbox2/)
