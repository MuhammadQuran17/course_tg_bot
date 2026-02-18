# Telegram Bot for Registration

## Configuration

Add these environment variables to your `.env` file:

```
# Telegram Bot Token (you already have this)
TG_BOT_API_TOKEN=your_bot_token_here

# Database Configuration
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=your_password
DB_NAME=telegram_bot

# Telegram Channel ID (where to send notifications)
TELEGRAM_CHANNEL_ID=-1000000000
```

### How to Get Your Telegram Channel ID:

1. Create a Telegram channel (private or public)
2. Add your bot as an administrator
3. Send a message to the channel
4. Use the Json Dump Bot or API:
   - Send a message to the channel
   - Visit: `https://api.telegram.org/bot<YOUR_TOKEN>/getUpdates` 
   - (if you have set webhook, this api will not work. Then you can get it also from Json Dump Bot. Just send a message to the bot and you will see the chat_id in the response json data)
   - Look for the chat_id in the response (it starts with -100)

## Database Setup

### 1. Create the Database
```sql
CREATE DATABASE telegram_bot
```

### 2. Create the Users Table
Run the SQL script in `database/schema.sql`:

```bash
mysql -u root -p telegram_bot < database/schema.sql
```

Or manually execute the SQL in your database client.

## Data Flow

1. **User sends `/start` command** → Bot starts RegistrationConversation
2. **Bot asks for name and programming language** → User provides data
3. **Data is saved to MySQL** via UserRepository.saveUser()
4. **Notification is sent to Telegram channel** with registration details
5. **User sees confirmation message** in their DM

## Error Handling

- Database connection errors are caught and logged
- Telegram API errors are logged to PHP error log
- User receives a friendly error message if something goes wrong
- Check `error_log` for debugging

## Example Registration Message in Channel

```
✅ New Registration

Name: John Doe
Programming Language: PHP
Telegram ID: 123456789
Time: 2026-02-16 10:30:45
```