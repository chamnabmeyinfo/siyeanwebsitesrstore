---
name: report-summary-results-focus
description: Summarizes each user message into key points and keeps assistant replies outcome-focused with concrete results. Use when the user wants message summaries, condensed takeaways, results-first communication, or when they invoke chat reporting / result-oriented focus.
---

# Report, summarize, results-first

## Verbatim text from the user

Please Always Report Every Message I chat. Summary the Key Point. I want to focus on Result oriented.

---

## Instructions

Apply **every** time this skill is in context (or when the user asks for reporting / summary / results focus).

### 1. Reflect the latest user message

Start the substantive reply with a **short block** (3–6 bullets or one tight paragraph) that states:

- What they asked or decided (the **key point** of *this* message).
- Any constraint they repeated or newly added.

Do **not** replace the answer with only a summary—the summary precedes or frames the deliverable.

### 2. Result-oriented body

After the summary, give the **result**:

- **Done / answer**: what they get (facts, steps, code paths, commands, decisions).
- **Artifacts**: file paths, commit ideas, or URLs only when useful—no filler.

Prefer:

- Headings + bullets over long prose.
- One primary **outcome** line when applicable (e.g. “Outcome: tests pass; push commit `abc1234`.”).

Avoid:

- Long preambles, hedging chains, or repeating the entire chat unless they asked for a full recap.

### 3. Full-thread recap only on request

If they ask to “summarize the whole chat” or “everything we said,” then produce a **structured recap** (topics + decisions + open items), still **result-oriented**.

### 4. Length

Match depth to complexity: small ask → short summary + short result; large task → brief summary + structured result sections.

---

## Example shape

```markdown
**Your message (key points):**
- …
- …

**Result:**
- …
```

(Adapt headings to match the project’s tone; keep the split between “what you focused on” and “what you get.”)
