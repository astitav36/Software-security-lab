# Software Security Lab Experiments

This repository contains the practical work completed for the Software Security Lab course. It includes all 10 experiments from the lab syllabus, along with code, reports, screenshots, and supporting files used during implementation.

## Objective

The goal of this repository is to demonstrate core software security concepts through hands-on experiments such as vulnerability identification, secure coding, hashing and salting, traffic inspection, threat modeling, authentication, vulnerability scanning, browser artifact analysis, and incident response.

## List of Experiments

| S. No. | Experiment Title | Repository Folder |
| --- | --- | --- |
| 1 | Analyze sample code snippets to identify basic security flaws | `Exp1/` |
| 2 | Simulate input-based attacks and implement necessary protections | `Exp2/` |
| 3 | Perform cryptographic hashing and secure password storage | `EX 3/` |
| 4 | Conduct secure code review of a login system | `Exp 4/` |
| 5 | Capture and inspect network traffic | `Exp 5/` |
| 6 | Design and analyze a threat model for a web app | `Exp 6/` |
| 7 | Explore authentication mechanisms and implement login with password hashing | `Exp 7/` |
| 8 | Scan a system or web app for common vulnerabilities | `Exp 8/` |
| 9 | Inspect browser artifacts: history, cookies and saved data | `Exp 9/` |
| 10 | Simulate a simple incident response based on sample logs | `Exp 10/` |

## Experiment Summary

### Experiment 1: Security Flaw Identification
- Studied insecure C and Python code samples.
- Identified vulnerabilities such as buffer overflow, format string misuse, unsafe OS command execution, and hard-coded credentials.
- Compared vulnerable and fixed implementations.

### Experiment 2: Input-Based Attacks and Protections
- Worked with a sample login application.
- Observed how insecure input handling can lead to attacks such as SQL injection.
- Applied safer handling and validation techniques.

### Experiment 3: Hashing and Secure Password Storage
- Implemented password hashing using Python and Flask.
- Demonstrated hashing-only and hashing with salting.
- Showed that plaintext passwords should never be stored in the database.

### Experiment 4: Secure Code Review of Login System
- Reviewed a login module for insecure coding practices.
- Compared insecure and improved versions of the login flow.
- Focused on issues like plaintext password handling and missing checks.

### Experiment 5: Network Traffic Inspection
- Captured and inspected traffic related to login communication.
- Studied how HTTP can expose sensitive information in plaintext.
- Documented observations in the attached output/report files.

### Experiment 6: Threat Modeling
- Designed and analyzed threats for a sample web application.
- Used structured thinking to identify attack surfaces and possible risks.
- Included supporting diagrams and report material.

### Experiment 7: Authentication with Password Hashing
- Built a simple authentication workflow.
- Used hashed password verification during login.
- Demonstrated a more secure authentication approach than plaintext storage.

### Experiment 8: Vulnerability Scanning
- Performed vulnerability assessment using scan reports.
- Worked with OWASP ZAP output and related report files.
- Reviewed findings for common issues and misconfigurations.

### Experiment 9: Browser Artifact Analysis
- Examined browser-related artifacts such as history, cookies, and saved data.
- Studied privacy and tracking implications.
- Documented observations in report files.

### Experiment 10: Basic Incident Response
- Analyzed sample logs to identify suspicious activity.
- Looked for patterns such as repeated login attempts or abnormal events.
- Prepared material useful for a simple incident response report.

## Repository Structure

- `Exp1/` contains vulnerable and fixed code examples for common software security flaws.
- `Exp2/securitylab/` contains the web application used for input-handling experiments.
- `EX 3/` contains the hashing and salting demonstration project.
- `Exp 4/` and `Exp 5/` contain login-related code and lab output files.
- `Exp 6/`, `Exp 8/`, and `Exp 9/` mainly contain report material, PDFs, and screenshots.
- `Exp 7/` contains the authentication project with password hashing.
- `Exp 10/` contains logs and supporting files for incident-response analysis.
- `STRIDE with ZAP/` is an additional project that connects threat modeling with ZAP scan analysis.

## Tools and Technologies Used

- C
- Python
- Flask
- PHP
- MySQL / XAMPP
- OWASP ZAP
- Wireshark or similar packet analysis tools
- Browser developer tools

## Notes

- Some experiments are code-based, while others are report-based.
- Certain folders include PDFs, screenshots, JSON reports, or sample logs as part of the submission.
- A few folders also contain supporting or alternate versions of the same experiment work.

## Course Context

This work is based on the Software Security Lab syllabus for B.Tech CSE (Cyber Security) and covers foundational practical exercises in secure coding, vulnerability analysis, and security testing.
