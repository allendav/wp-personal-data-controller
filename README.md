# wp-personal-data-controller
A proof of concept feature plugin to support core and other plugins in handling of personal data, especially with regard to privacy and the GDPR. Inspired by the GDPRWP effort.

## Design

* Given the data subject’s email address
* Core fetches the user for that email address (if any)
* Next, core loops over ALL posts (and custom post types) (excellent way to leverage SQL LIMIT)
  * if we have a registered user for the data subject’s email address and they are the post author
    * core initializes a “findings” array with the personal data it knows about in the post and post-meta
    * core runs a filter to allow plugins to filter in their own personal data findings for that post, if any
  * else (user isn’t post author)
    * core runs a different filter to allow plugins to filter in their own personal data findings for that post, if any
  * lastly, core adds the findings, if any, to the overall export array, under a post and postID structure

* Next, core does something similar for ALL comments
  * If we have a registered user for the data subject’s email address and they are the comment author
    * core initializes a “findings” array with the personal data it knows about in the comment and comment-meta
    * core runs a filter to allow plugins to filter in their own personal data findings for that comment, if any
  * else (user isn’t comment author)
    * core runs a different filter to allow plugins to filter in their own personal data findings for that comment, if any
  * lastly, core adds the findings, if any, to the overall export array, under a comment and commentID structure

* Next, core does something similar for ALL links (are links even still a thing, lol - they are in the DB schema)
  * if we have a registered user for the data subject’s email address and they are the link owner
    * core initializes a “findings” array with the personal data it knows about in the link
    * core runs a filter to allow plugins to filter in their own personal data findings for that link, if any
  * else (user isn’t link owner)
    * core runs a different filter to allow plugins to filter in their own personal data findings for that link, if any
  * lastly, core adds the findings, if any, to the overall export array, under a comment and commentID structure

* Next, if we have a registered user for the data subject’s email address, core grabs all usermeta for that user
  * core initializes a “findings” array with the personal data it knows about in usermeta
  * core runs a filter to allow plugins to filter in their own personal data findings for that user, if any
  * lastly, core adds the findings, if any, to the overall export array, under a “user” key

* Next, core creates a “findings” array for options
  * core initializes the array with the personal data for the data subject’s email address it knows about in options (like admin_email), if any
  * core runs a filter to allow plugins to filter in their own personal data findings for the data subject’s email address, if any
  * lastly, core adds the findings, if any, to the overall export array, under a “options” key

* Lastly, core creates an “findings” array for “other”
  * this will be a good place to captur e personal data that doesn’t live in posts, comments, usermeta, links or options
  * core initializes the array with an empty array
  * core runs a filter to allow plugins to filter in their own personal data findings for the data subject’s email address, if any
  * lastly, core adds the findings, if any, to the overall export array, under an “other” key

The export will end up looking something like (e.g. comment ID 12)

```
array(
 'comments' => array(
  12 => array(
   array(
    'type' => 'comments',
    'key' => 'comment_author_email',
    'value' => 'foo@example.com',
   ),
   array(
    'type' => 'comments',
    'key' => 'comment_agent',
    'value' => 'Mozilla/5.0...',
   ),
   ...
   array(
    'plugin_slug' => 'my_lat_long_plugin',
    'type' => 'commentmeta',
    'key' => 'comment_author_lat',
    'value' => 47.6,
   ),
   array(
    'plugin_slug' => 'my_lat_long_plugin',
    'type` => `commentmeta',
    'key' => `comment_author_long',
    'value' => -122.3,
   )
  ),
 ‘posts’ => array( ... ),
 ...
);
```
