# Draft

Draft is a PHP static site creator written in [Driftphp](https://driftphhp.io). This is running my personal blog https://developernaren.com

## Ugh!? Another static site generator!? why?? and that in PHP??
I have been working in PHP for more than 10 years. I have worked with nodejs and golang in few of those years. 
I always felt like PHP was put down as a starter language which people learn to get into programming. 
After few years in PHP, people would be pressured into learning a "better" language. Even though I worked with nodejs and golang, PHP always felt the most comfortable to me.
When I heard about [reactphp](https://reactphp.org/), I wanted to get into it as soon as I could, but there were no starter template of sorts to get started.
With Driftphp, I felt there finally is a framework that I can comfortably start working with it.

## Enough complaining! Tell me how it works.
Alright, Alright. Draft is a static site generator. It can parse `.html` and `.md` files and generate a fully html page. 
It supports `html` layouts and content can be either `html` and `md` files.

### `<draft>`
We include meta for post in a `<draft>` tag. Currently it supports
  
- layout
- title
- description
    
    Example
    ```
    <draft>
        title: This is a ttest
        description: This is the description
        layout: blog.html
    </draft>
    ```
Refer to this file for [example](/Drift/views/blogs/index.html) 
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
        
 





