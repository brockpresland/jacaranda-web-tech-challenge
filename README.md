# Website Developer - Take-home Challenge

> **Time:** aim for about 1 hour. Stop at 90 minutes and submit what you have.
> **AI:** use it if you want. We do. But you will walk us through every choice, so understand your code.
> **Commits:** commit as you go, small and sensible. We read the history.

## The scene
You have joined the web team at our company (a consumer lender). We run a WordPress marketing site with some custom PHP, a MySQL back end, and a few Vue widgets. Two small jobs have landed on your desk this morning.

## Before you start - time and AI
- **Aim for about an hour.** If you reach 90 minutes, stop and submit what you have. Unfinished is fine. We care how you work, not whether you finish every line.
- **Use AI tools if you want to.** We do. But you will walk us through your code and your decisions afterwards, so make sure you understand and can defend every choice. We are hiring your judgement, not your typing speed.
- **Commit as you go** - small, sensible commits. We read the history.

## Part A - Review and harden (PHP / WordPress / MySQL) ~30-35 min
A teammate asked an AI tool to write a shortcode that lists our loan products on a page. It "works", but we are not comfortable shipping it.

In `part-a/rate-table.php`:
1. Review the code and fix the problems you find.
2. Add a short comment above each fix saying what was wrong.

You do **not** need a running WordPress site. Treat this as a code review and fix.

## Part B - A small Vue widget (Vue 3) ~20-25 min
We want a simple, searchable product list for the site.

In `part-b/`:
1. Finish the `ProductList` component so it renders the products from `products.json`.
2. Add a search box that filters by product name as you type.
3. Add a category filter.
4. Show each product's name, lender, and rate (rate to 2 decimal places), plus a result count and a sensible empty state.

Run it with `npm install && npm run dev`.

## Decisions
Fill in `DECISIONS.md`: pick **3-4 choices in total, drawn from across both tasks** (A and B), and write one line each on why you did it that way, and one thing you chose **not** to do (and why).

## What we are looking for
- Code that is correct, safe, and easy to read
- Good instincts on security and performance
- Clear, honest decisions in your own words
- Small, sensible commits

There are no trick questions. Do what you would do on a normal work day, and be ready to talk us through it.

## How to submit
This folder is already a git repository with one initial commit. Build your work on top of it.

1. Keep working in this folder. Commit as you go - small, sensible commits.
2. Create a new **public** repository on any git host (GitHub, GitLab, or Bitbucket).
3. Add it as a remote and push your full history:
   ```
   git remote add origin <your-public-repo-url>
   git push -u origin main
   ```
4. Send us the repository link.

Please do not squash your history into a single commit. We read how you work.
