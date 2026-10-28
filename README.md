# dreamspace.academy

The published output of **the DreamSpace Academy website**, the public reference for who DreamSpace
Academy is, why the work exists, how the whole model fits together, and the education system and
MakerSpace inside it.

**Live at <https://dreamspace.academy>**

This repository was called `model.dreamspace.academy` until 28 October 2026, when the site had
already moved to the main domain. The previous website's repository, which held this name, is now
`archive.dreamspace.academy`.

## ⛔ Do not edit this repository

Everything here is **generated**. This repository holds the built static site and nothing else.

- The **source** lives in a separate repository, alongside the model and the evidence base the site
  is written from. Several pages are generated from that repository at build time, so a change made
  here would be silently overwritten on the next deploy and would lose the trail back to what it
  was written from.
- The `gh-pages` branch is **force-replaced on every build**. Anything committed to it disappears
  when the build next runs, which is also why the `CNAME` file is written by the build each time
  rather than committed by hand. A hand-committed one would be wiped and the custom domain would
  quietly revert.

To change something on the site, change it at the source and let the build publish it.

## How it gets here

A GitHub Actions workflow in the source repository builds the site on every push that touches it,
checks that the build actually produced pages, and force-pushes the output to `gh-pages` here.
GitHub Pages serves that branch on the custom domain. Publishing uses an SSH deploy key scoped to
this repository alone, so it can reach nothing else and is not tied to any individual's account.

## What the site covers

| Section | What it holds |
|---|---|
| **The organisation** | Identity, why the work exists, the theory of change, the DreamSpace Lifecycle, labs and research, ventures and incubation, impact and the global goals, and how the work is funded |
| **The education system** | How learning is structured, taught and evidenced: the journey, facilitation, assessment, and the programmes |
| **The MakerSpace** | The model the education runs in: the domains, the enterprise strand, the venture pathway, replication and measurement |
| **Library** | Questions, articles and essays, a controlled vocabulary, and the published works the site draws on |

It is written for educators, partners, funders and the communities DreamSpace Academy works with.
Where something is a hypothesis rather than a result, the site says so in the sentence that makes
the claim.

## About DreamSpace Academy

A non-profit social enterprise that co-creates an ecosystem to tackle local socio-economic and
environmental challenges through education, innovation and entrepreneurship. Founded in Batticaloa,
Sri Lanka, in 2018.

<https://dreamspace.academy>
