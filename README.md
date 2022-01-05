# Rath3r

This is the frontend main page of rath3r.com.

_TLDR_ A React app using TypeScript

# Design Decisions

This site has gone through many interations. Languages and frameworks used have ranged from jQuery
to React. The backend language of choice was for a long time PHP but JS has usurped its position.

The current iteration is an attempt to keep current and to use more effectively the stack which I
use professionally. The main site of the [ROH][1] is a React frontend served from an Express
configured server side rendered app.

For this site I have chosen to use [NextJs][2] after an overly arduce attempt to configure a
nodemon dev server.

## Installation

The output of `npx create-next-app nextjs-blog --ts` has been copied on top of the existing app.

## Dependencies

I'm trying to keep at `"node": "lts"` using [NVM][3] which is `16.13.1`.

[1]: https://www.roh.org.uk/
[2]: https://nextjs.org/
[3]: https://github.com/nvm-sh/nvm
