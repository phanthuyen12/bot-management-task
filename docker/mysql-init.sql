-- Initialize database
-- Run basic optimizations

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Create indexes for better performance
ALTER TABLE users ADD INDEX idx_email (email);
ALTER TABLE users ADD INDEX idx_created_at (created_at);

-- Set default collation
ALTER DATABASE taskmanagentbot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
