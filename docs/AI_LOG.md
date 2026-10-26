# AI_LOG.md

Record of AI tool usage for this project, one section per milestone.

## Milestone 1

**Tool used:** Claude (Claude Code), used for the full Milestone 1 scope: setting up the project folder structure, building the static SPA frontend (all 13 pages), the admin featured-products feature, the checkout flow and design cleanup.

**Parts the AI worked on:**
- Project folder structure (`frontend/`, `backend/`, `.gitignore`) per the Milestone 1 requirement 1 wording.
- All static frontend pages (home, shop, product detail, cart, checkout, order confirmation, login, register, profile, about, contact, admin, 404) as a hash-routed single-page application.
- Visual design: color palette, typography, layout, and real product photography.
- The admin "featured products" toggle (localStorage-backed, max 4).
- Client-side checkout validation.
- Drafting this documentation set (`AGENTS.md`, `README.md`, `CLAUDE.md`, this file).

**Key prompts used:**
- "find a folder Pet Shop on desktop and there we will do everything" - set the working directory for the whole project.
- The pasted Milestone 1 requirement 1 text (project structure: frontend/backend folders, `.gitignore`).
- The pasted Milestone 1 requirement 2 text (static frontend, SPA, required pages), with the added instruction to keep the design realistic for a student project, avoid downloaded templates and heavy animation, and ask 5 clarifying questions first.
- Answers to those 5 questions: products-only shop, Customer and Shop Admin roles, dog/cat breed facts as the planned external API, a warm pastel theme, and a Bosnia-and-Herzegovina-only checkout with cash on delivery and no payment gateway.
- "ok just make sure that design isn't tipically AI BASED you can search for ui ux skill and try to use something there ... also you have to use real pictures and now it says when I open index.html file could not load this page please fix that" - led to using the UI/UX skill for layout guidance, replacing emoji with real photos, and an early fix for opening the page without a server.
- "make homepage more rich with for example more sections such as maybe with qoute or something like that, add a option to switch feature products through the admin dashboard so we can change maybe newer product or something like that(max 4 feature products) and add check of the street phone number email etc inside chechout process so we cannot order something without any data" - added the testimonials section, the admin featured-products toggle, and checkout validation.
- "maybe you should add one more section to the homepage ... from register form remove phone number cuz we dont need it and inside contact page where it says street franje ... change to Francuske revolucije bb, Sarajevo" - added the "How it works" section, removed the register phone field, fixed the store address.
- "also go through the code and remove all comments cuz we dont need anyhting ... also create and .md file only for you in case I open a new session you dont need to go again through the whole code base ... write it as detailed as you can and update it every time you change/add/delete something" - removed all code comments and created `CLAUDE.md`.
- "now lets remove this AI generated dashes - and replace it with -" - swept em dashes out of all page copy and docs.
- "tell me now why you use python and is there some document that says we should use python ... never used languages or techs that are not provided and marked as able to use inside project documentations so only php js mysql" - removed the Python build script and local server tooling, reverted the router to plain `fetch()`.
- "you write me a agents.md file and also a readme file" - created `AGENTS.md` and `README.md`.
- "ok write also that file and 2 cases where AI missed something mention no validations on field and placing emotes instead of images where i see these emotes and i tried checkout form and i see no validations" - created this file with the two cases below.

**Cases where the AI was wrong or produced poor code:**

1. **Emoji used instead of real images.** The AI's first version of the frontend used emoji characters (a bone, a fish, paw prints, and so on) as the hero image and as every product thumbnail, instead of real photography. This was noticed when reviewing the site and asking the AI to make the design look less generic and less obviously AI-generated. Fix: all emoji imagery was replaced with real stock photos (one per product, plus a hero photo and an about-page photo, sourced from Unsplash), and emoji used as icons elsewhere in the UI were replaced with Bootstrap Icons.

2. **No real validation on the checkout form.** The AI's first version of the checkout form had `required` attributes on its fields but also set `novalidate` on the `<form>` and never wrote any JavaScript to check the field values before submitting. This meant the checkout could be submitted with every field empty and would still silently create an order. Noticed by manually testing the checkout page and submitting it with blank fields. Fix: added client-side validation (name, phone, email, street, city, postal code) that shows an inline error under each invalid field and blocks the order from being placed until every field passes, with a note that server-side validation still needs to be added once the backend exists in Milestone 3, since client-side checks alone can be bypassed.
