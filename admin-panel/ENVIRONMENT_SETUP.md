# Environment Setup Guide

## Creating Environment Files

The environment files are not tracked in Git for security reasons. You need to create them manually before running the application.

### 1. Development Environment

Create `src/environments/environment.ts`:

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api',
  apiTimeout: 30000
};
```

### 2. Production Environment

Create `src/environments/environment.prod.ts`:

```typescript
export const environment = {
  production: true,
  apiUrl: 'https://your-production-api.com/api',
  apiTimeout: 30000
};
```

### Quick Setup Command

You can copy from the example file:

```bash
# For development
cp src/environments/environment.example.ts src/environments/environment.ts

# For production
cp src/environments/environment.example.ts src/environments/environment.prod.ts
```

Then edit the files to match your API configuration.

## Configuration Options

- **apiUrl**: The base URL of your backend API
- **apiTimeout**: Request timeout in milliseconds (default: 30000 = 30 seconds)
- **production**: Set to `true` for production builds

## Important Notes

⚠️ **Never commit environment files to Git!**

These files contain sensitive configuration and are already listed in `.gitignore`:
- `**/environments/environment.ts`
- `**/environments/environment.prod.ts`

## Troubleshooting

If you get compilation errors about missing environment files:

1. Make sure both `environment.ts` and `environment.prod.ts` exist
2. Check that they export the correct structure
3. Verify the `apiUrl` is correct for your setup

## Example API URLs

- **Local Development**: `http://localhost:8000/api`
- **Docker**: `http://backend:8000/api`
- **Production**: `https://api.yourdomain.com/api`
