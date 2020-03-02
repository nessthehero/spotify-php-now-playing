# Icomoon.io

At BarkleyREI, we refer to SVG icons using a pattern where the icons are declared in a massive SVG element at the 
start of the page and referred to using <use: tags.

Icomoon generates the files you need to get started.

1. Create a project on Icomoon.
    - Log into the website using Barkley credentials. If a project already exists, use that, otherwise please 
        create a new one.
    - Projects represent real large-scale projects. If a project for a client already exists but it's a new grouping of 
        work, create a new project.
        
2. Add icons to the project.
    - Try to group the icons together instead of leaving disparate sets. The only exception to this is if the icon 
    needs to be uniquely sized (like a wide logo) or it has unique colors baked in, it can be in its own set. Try to 
    combine sets into as few as possible. Name the sets appropriately (e.g. Logo) if they contain specific icons, otherwise use a 
    single set named after the client.
    
3. Generate SVG package.
    - Click SVG at the bottom under the sets and then click download. If you need to clean up the names of any icons,
     do it on this screen before downloading.
     
4. Expand the archive and dump its contents into /src/static/app/sass/icons. You can include all the demo files if 
you wish. 
    - The following files MUST be part of the repo and versioned:
        * SVG and PNG folders
        * selection.json
        * symbol-defs.svg
        * style.css
        
5. Save and rename style.css as _style.scss

6. If you haven't already, add `pointer-events: none;` to the main .icon/.brei-icon class. This prevents unwanted click events in JavaScript.

7. Uncomment the line in main.scss to refer to _style.scss in the icons folder.

8. Copy the contents of symboldefs.svg into /src/static/app/assemble/includes/_svg.hbs

## Using Icons

To refer to an icon, use the following syntax:

    <svg class='icon icon-ICON_NAME' aria-hidden='true'><use xlink:href='#icon-ICON_NAME'></use></svg> 
    
Replace ICON_NAME with the name of the icon from icomoon.

## Namespacing

Some CMS systems use `.icon` as a class in major functionality. To avoid this, namespace the icons with something like `brei-`. You can add the namespace by modifying the class prefix and class selector of the project in the settings on Icomoon.
