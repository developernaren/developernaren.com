# Draft

Draft is a PHP static site creator written in [Driftphp](https://driftphhp.io). This is running my personal blog https://developernaren.com

## Ugh!? Another static site generator!? why?? and that in PHP??
This is a hobby project for me. I wanted to try out async PHP. When I heard about [reactphp](https://reactphp.org/).I wanted to get into it as soon as I could, but there were no starter template of sorts to get started.
With Driftphp, I felt there finally is a framework that I can comfortably start working with it.

## Enough complaining! Tell me how it works.
Alright, Alright. Draft is a static site generator. It can parse `.html` and `.md` files and generate a fully html page. 
It supports `html` layouts and content can be either `html` and `md` files.

### `<draft>`
We include 'meta' for post in a `<draft>` tag. Meta here means whatever you want to be replaced in the content of the page.

> `{content}` in the layout and `layout` in the `<draft>` tag are reserved and cannot be used to replace the contents in the page

The syntax in the template is `{meta}`.
For example if you want to add title to a page `{title}`, add a 
```
title: This is the test title
```
in your draft tag. 
and in your layout. You would add

```html
...
<title>{title}</title>
...

```

This will generate 
```html
<title>This is the test title</title>
```
in the HTML.
  
    Example draft tag
    ```
    <draft>       
        description: this is the best description
        title: This is the test title
        layout: blog.html
    </draft>
    ```
Refer to this file for [example](/drarft/blogs/index.html) 
## Todos

- [ ] Refactor to make it adaptable
- [ ] Tests
- [ ] Cache Support
- [ ] Build process to generate static html pages
- Configurable
    - Options
        - [ ] Base Route
        - [ ] Layout path
        - [ ] Cache Path and driver
        - [ ] Better Seo support
        - [ ] Hot reload for md changes
        
I feel like there is only so much a static site generator should be able to do, but feel free to add things you would like to see here.
        
 





