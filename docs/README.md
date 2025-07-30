# BTCS Coach Documentation

This folder contains documentation for the BTCS Coach application.

## Files

- **voiceflow-integration.md** - Complete documentation on how data is passed to Voiceflow based on different routes, including variable access and implementation details.

## Quick Reference

### Voiceflow Data Structure

```javascript
payload: {
  route: {
    name: 'modules.chat',
    path: '/modules/{slug}/chat',
    params: { slug: 'module-slug' }
  },
  module: { /* module data */ },
  user: { /* user data */ },
  session_context: 'pi_ssl_coaching'
}
```

### Key Variables Available in Voiceflow
- `{route.name}` - Current route name
- `{module.title}` - Module name
- `{user.name}` - User's name
- `{session_context}` - Context identifier

See `voiceflow-integration.md` for complete details.