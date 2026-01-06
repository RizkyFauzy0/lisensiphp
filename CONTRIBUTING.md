# Contributing to License Management System

Thank you for your interest in contributing to the License Management System! This document provides guidelines and instructions for contributing.

## 🤝 How to Contribute

### Reporting Bugs

If you find a bug, please create an issue with:
- Clear title describing the bug
- Steps to reproduce
- Expected behavior
- Actual behavior
- Screenshots (if applicable)
- Your environment (PHP version, MySQL version, OS)

### Suggesting Features

Feature requests are welcome! Please create an issue with:
- Clear title
- Detailed description
- Use case/motivation
- Possible implementation approach (optional)

### Pull Requests

1. **Fork the repository**
   ```bash
   git clone https://github.com/RizkyFauzy0/lisensiphp.git
   cd lisensiphp
   ```

2. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Make your changes**
   - Follow the existing code style
   - Add comments for complex logic
   - Update documentation if needed

4. **Test your changes**
   - Ensure no existing functionality is broken
   - Test all related features
   - Verify security implications

5. **Commit your changes**
   ```bash
   git add .
   git commit -m "Add: description of your changes"
   ```

6. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

7. **Create a Pull Request**
   - Provide a clear title and description
   - Reference any related issues
   - Include screenshots for UI changes

## 📝 Coding Standards

### PHP Code Style

- Use PSR-12 coding standard
- Use meaningful variable and function names
- Add PHPDoc comments for classes and methods
- Keep functions small and focused
- Use type hints where possible

Example:
```php
<?php
/**
 * Validate license with the server
 * 
 * @param string $apiKey The API key to validate
 * @param string $domain The domain to check
 * @return array Validation result
 */
public function validateLicense(string $apiKey, string $domain): array
{
    // Implementation
}
```

### Database Queries

- Always use prepared statements
- Never concatenate user input in SQL
- Use meaningful column names
- Add indexes for frequently queried columns

Example:
```php
// Good ✓
$stmt = $this->db->query("SELECT * FROM users WHERE id = ?", [$id]);

// Bad ✗
$result = $this->db->query("SELECT * FROM users WHERE id = $id");
```

### HTML/CSS

- Use semantic HTML5 elements
- Follow Tailwind CSS utility classes
- Keep inline styles minimal
- Ensure responsive design
- Add ARIA labels for accessibility

### JavaScript

- Use modern ES6+ syntax
- Keep functions pure when possible
- Add comments for complex logic
- Avoid jQuery (use Alpine.js or vanilla JS)

## 🔒 Security Guidelines

- Never commit sensitive data (passwords, API keys)
- Always validate and sanitize user input
- Use prepared statements for SQL queries
- Escape output to prevent XSS
- Follow OWASP security best practices
- Hash passwords with bcrypt
- Implement CSRF protection

## 🧪 Testing

Before submitting a PR:

1. **Manual Testing**
   - Test all affected functionality
   - Check edge cases
   - Verify error handling

2. **Cross-browser Testing**
   - Chrome
   - Firefox
   - Safari
   - Edge

3. **Responsive Testing**
   - Mobile devices
   - Tablets
   - Desktop

4. **Security Testing**
   - SQL injection attempts
   - XSS attempts
   - CSRF protection
   - Authentication bypass

## 📚 Documentation

Update documentation when:
- Adding new features
- Changing existing functionality
- Modifying API endpoints
- Updating database schema
- Changing configuration options

Documentation to update:
- README.md (main documentation)
- CHANGELOG.md (version history)
- FEATURES.md (feature list)
- Inline code comments
- API documentation

## 🎯 Priority Areas

We especially welcome contributions in:

1. **Security Enhancements**
   - Security audits
   - Vulnerability fixes
   - Penetration testing

2. **Performance Optimization**
   - Query optimization
   - Caching implementation
   - Code optimization

3. **Feature Additions**
   - Email notifications
   - Export functionality
   - Advanced analytics
   - API enhancements

4. **UI/UX Improvements**
   - Accessibility
   - Mobile optimization
   - Dark mode
   - Better error messages

5. **Testing**
   - Unit tests
   - Integration tests
   - End-to-end tests

6. **Documentation**
   - Code comments
   - User guides
   - Video tutorials
   - Translation

## 🚫 What NOT to Contribute

Please avoid:
- Adding dependencies without discussion
- Making breaking changes without approval
- Submitting incomplete work
- Copying code from other projects without proper attribution
- Making cosmetic changes without functional improvements

## 💬 Communication

- **GitHub Issues**: For bug reports and feature requests
- **Pull Requests**: For code contributions
- **Discussions**: For questions and general discussion

## 📋 Checklist for Pull Requests

Before submitting, ensure:

- [ ] Code follows project style guidelines
- [ ] All tests pass
- [ ] Documentation is updated
- [ ] Commit messages are clear
- [ ] No merge conflicts
- [ ] Code is properly commented
- [ ] Security implications considered
- [ ] Backward compatibility maintained (or documented)
- [ ] Performance impact considered
- [ ] Screenshots included (for UI changes)

## 🏆 Recognition

Contributors will be recognized in:
- CHANGELOG.md
- README.md (Contributors section)
- Release notes

## 📄 License

By contributing, you agree that your contributions will be licensed under the MIT License.

## ❓ Questions?

If you have questions:
- Create a GitHub Issue
- Check existing documentation
- Review closed issues for similar questions

---

Thank you for contributing to make this project better! 🙏
