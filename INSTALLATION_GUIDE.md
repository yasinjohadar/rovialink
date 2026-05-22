# AI System Installation Guide

This guide provides step-by-step instructions for installing the AI system into a new Laravel project.

## Overview

The AI system includes comprehensive functionality for:
- AI Model Management (OpenAI, Anthropic, Google, Groq, Z.ai, Manus, etc.)
- Question Generation from content
- Question Solving with AI
- Blog Post Generation with AI
- Content Summarization and Improvement
- Student Feedback Generation
- Essay Grading with AI
- Multi-provider support with unified interface

## Prerequisites

### Required Laravel Version
- Laravel 10.x or higher
- PHP 8.2 or higher

### Required Dependencies

Add these packages to your `composer.json`:

```json
{
    "require": {
        "guzzlehttp/guzzle": "^7.0",
        "laravel/framework": "^10.0"
    }
}
```

Run:
```bash
composer update
```

## Installation Steps

### Step 1: Copy Files

1. **Copy Models** (7 files)
   - Copy all files from `01-Models/` to `app/Models/`
   - Files:
     - `AIModel.php`
     - `AISetting.php`
     - `AIConversation.php`
     - `AIMessage.php`
     - `AIQuestionGeneration.php`
     - `AIQuestionSolution.php`
     - `AIStudentFeedback.php`

2. **Copy Controllers** (9 files)
   - Copy all files from `02-Controllers/` to `app/Http/Controllers/Admin/`
   - Files:
     - `AIBlogPostController.php`
     - `AIModelController.php`
     - `AIQuestionCreationController.php`
     - `AIQuestionGenerationController.php`
     - `AIQuestionSolvingController.php`
     - `AIStudentFeedbackController.php`
     - `AIContentController.php`
     - `AISettingsController.php`
     - `AIGradingSettingsController.php`

3. **Copy Services** (22 files)
   - Copy all files from `03-Services/` to `app/Services/Ai/`
   - Files:
     - `AIProviderService.php` (Abstract base class)
     - `AIProviderFactory.php`
     - `AIModelService.php`
     - `AIPromptService.php`
     - `AIBlogPostService.php`
     - `OpenAIProviderService.php`
     - `AnthropicProviderService.php`
     - `GoogleProviderService.php`
     - `OpenRouterProviderService.php`
     - `ZaiProviderService.php`
     - `ManusProviderService.php`
     - `GroqProviderService.php`
     - `GroqModelService.php`
     - `LocalLLMProviderService.php`
     - `AIChatbotService.php`
     - `AIContentImprovementService.php`
     - `AIContentSummaryService.php`
     - `AIEssayGradingService.php`
     - `AIQuestionCreationService.php`
     - `AIQuestionGenerationService.php`
     - `AIQuestionSolvingService.php`
     - `AIStudentFeedbackService.php`

4. **Copy Migrations** (9 files)
   - Copy all files from `04-Migrations/` to `database/migrations/`
   - Files:
     - `2025_12_31_093658_create_ai_models_table.php`
     - `2025_12_31_093715_create_ai_question_generations_table.php`
     - `2025_12_31_093724_create_ai_question_solutions_table.php`
     - `2025_12_31_093735_create_ai_student_feedback_table.php`
     - `2025_12_31_093744_create_ai_settings_table.php`
     - `2025_12_31_093804_create_ai_conversations_table.php`
     - `2025_12_31_093814_create_ai_messages_table.php`
     - `2026_01_09_174804_add_manus_to_ai_models_provider_enum.php`
     - `2026_01_13_120000_add_groq_to_ai_models_provider_enum.php`

5. **Copy Views** (15 files)
   - Copy all files from `05-Views/` to `resources/views/admin/`
   - Files:
     - `admin-blog-ai-posts-create.blade.php` → `resources/views/admin/blog/ai-posts/create.blade.php`
     - `admin-ai-models-index.blade.php` → `resources/views/admin/ai/models/index.blade.php`
     - `admin-ai-models-create.blade.php` → `resources/views/admin/ai/models/create.blade.php`
     - `admin-ai-models-edit.blade.php` → `resources/views/admin/ai/models/edit.blade.php`
     - `admin-ai-question-creation-create.blade.php` → `resources/views/admin/ai/question-creation/create.blade.php`
     - `admin-ai-question-generations-create.blade.php` → `resources/views/admin/ai/question-generations/create.blade.php`
     - `admin-ai-question-generations-index.blade.php` → `resources/views/admin/ai/question-generations/index.blade.php`
     - `admin-ai-question-generations-show.blade.php` → `resources/views/admin/ai/question-generations/show.blade.php`
     - `admin-ai-question-solutions-index.blade.php` → `resources/views/admin/ai/question-solutions/index.blade.php`
     - `admin-ai-question-solutions-show.blade.php` → `resources/views/admin/ai/question-solutions/show.blade.php`
     - `admin-ai-settings-index.blade.php` → `resources/views/admin/ai/settings/index.blade.php`
     - `admin-ai-settings-grading.blade.php` → `resources/views/admin/ai/settings/grading.blade.php`
     - `admin-ai-student-feedback-create.blade.php` → `resources/views/admin/ai/student-feedback/create.blade.php`
     - `admin-ai-student-feedback-index.blade.php` → `resources/views/admin/ai/student-feedback/index.blade.php`
     - `admin-ai-student-feedback-show.blade.php` → `resources/views/admin/ai/student-feedback/show.blade.php`

6. **Add Routes**
   - Open `routes/admin.php`
   - Copy all routes from `06-Routes/ai-routes.php`
   - Place them within the admin route group with auth and role:admin middleware
   - Note: Blog AI posts routes should be placed in the blog route group

### Step 2: Run Migrations

Run the database migrations to create the required tables:

```bash
php artisan migrate
```

This will create the following tables:
- `ai_models` - Stores AI model configurations
- `ai_question_generations` - Stores question generation requests
- `ai_question_solutions` - Stores AI-generated solutions
- `ai_student_feedback` - Stores student feedback generated by AI
- `ai_settings` - Stores AI system settings
- `ai_conversations` - Stores chat conversations
- `ai_messages` - Stores chat messages

### Step 3: Configure Environment

No additional environment configuration is required. The system uses Laravel's default configuration.

### Step 4: Create First AI Model

1. Access your application's admin panel
2. Navigate to AI → Models
3. Click "Create Model"
4. Configure your first AI model:
   - **Provider**: Choose from OpenAI, Anthropic, Google, OpenRouter, Z.ai, Manus, Groq, or Local LLM
   - **Model Key**: The model identifier (e.g., gpt-4, claude-3-opus, gemini-pro)
   - **API Key**: Your API key for the provider
   - **Capabilities**: Select which features this model supports:
     - Question Generation
     - Question Solving
     - Essay Grading
     - Content Summary
     - Content Improvement
     - Chat
   - **Temperature**: Set between 0-1 (recommended: 0.7)
   - **Max Tokens**: Set maximum tokens (recommended: 4096)
   - **Cost per 1K tokens**: Optional cost tracking
5. Click "Test" to verify the connection
6. Set as "Default" if this is your primary model

### Step 5: Configure AI Settings

1. Navigate to AI → Settings
2. Configure system-wide settings:
   - Default AI model for each capability
   - Cost tracking preferences
   - Rate limiting settings
   - Timeout configurations

## Supported AI Providers

### OpenAI
- Models: GPT-4, GPT-4 Turbo, GPT-3.5 Turbo
- API Key: Get from https://platform.openai.com/api-keys
- Documentation: https://platform.openai.com/docs

### Anthropic (Claude)
- Models: Claude 3 Opus, Claude 3 Sonnet, Claude 3 Haiku
- API Key: Get from https://console.anthropic.com/
- Documentation: https://docs.anthropic.com/

### Google (Gemini)
- Models: Gemini Pro, Gemini Ultra
- API Key: Get from https://aistudio.google.com/apikey
- Documentation: https://ai.google.dev/docs

### OpenRouter
- Access to multiple models from different providers
- API Key: Get from https://openrouter.ai/keys
- Documentation: https://openrouter.ai/docs

### Z.ai (GLM)
- Models: GLM-4.7, GLM-4
- API Key: Get from https://z.ai/subscribe
- Documentation: https://open.bigmodel.cn/dev/api

### Manus AI
- Custom AI provider
- API Key: Get from Manus documentation
- Documentation: Contact Manus for API details

### Groq
- Fast inference platform
- API Key: Get from https://console.groq.com/
- Documentation: https://console.groq.com/docs

### Local LLM
- Run local models (Ollama, Llama.cpp, etc.)
- Requires local server configuration

## Key Features

### 1. AI Model Management
- Multi-provider support with unified interface
- API key encryption (stored securely in database)
- Model testing before saving
- Capability-based model selection
- Priority and default model management

### 2. Question Generation
- Generate questions from lesson content
- Generate questions from manual text input
- Generate questions from topics
- Multiple question types: single choice, multiple choice, true/false, short answer
- Difficulty levels: easy, medium, hard, mixed
- Save generated questions to question bank
- Preview and regenerate options

### 3. Question Solving
- AI-powered question solving
- Confidence scoring
- Solution verification by admin
- Support for multiple questions at once

### 4. Blog Post Generation
- Full blog post generation with AI
- SEO optimization (meta tags, keywords, focus keyword)
- Open Graph tags
- Twitter Card tags
- Schema.org markup
- Keyword synonyms generation
- Reading time calculation
- Content length options (short, medium, long)
- Tone options (professional, friendly, technical, casual, formal)

### 5. Content Tools
- **Summarization**: Short, long, or bullet points
- **Improvement**: General, grammar-focused, clarity-focused
- **Grammar Check**: Detect and fix errors
- **Lesson Summary**: Auto-generate summaries for lessons

### 6. Student Feedback
- Performance-based feedback
- General feedback
- Improvement suggestions
- Quiz attempt analysis
- Custom prompts for personalized feedback

### 7. Essay Grading
- AI-powered essay grading
- Customizable grading criteria
- Multiple criteria support
- Detailed feedback with strengths and weaknesses
- Suggestions for improvement

### 8. AI Chatbot
- Context-aware conversations
- Lesson-specific chat
- Course-specific chat
- General chat mode
- Message history tracking
- Token and cost tracking

## Configuration Requirements

### Database Configuration
The migrations will create tables with the following structure:

```sql
-- AI Models
CREATE TABLE ai_models (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    provider ENUM('openai', 'anthropic', 'google', 'openrouter', 'zai', 'manus', 'groq', 'local', 'custom'),
    model_key VARCHAR(255),
    api_key TEXT (encrypted),
    api_endpoint VARCHAR(500),
    base_url VARCHAR(500),
    max_tokens INT,
    temperature DECIMAL(3,2),
    is_active BOOLEAN DEFAULT 1,
    is_default BOOLEAN DEFAULT 0,
    priority INT DEFAULT 0,
    cost_per_1k_tokens DECIMAL(10,4),
    capabilities JSON,
    settings JSON,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- AI Settings
CREATE TABLE ai_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(255) UNIQUE,
    value TEXT,
    type ENUM('string', 'integer', 'boolean', 'json'),
    description TEXT,
    is_public BOOLEAN DEFAULT 0,
    category VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Troubleshooting

### Common Issues

**Issue**: API Key not working
- **Solution**: Verify the API key is correct and active
- Check if the provider is experiencing outages
- Test the connection using the "Test" button

**Issue**: Questions not generating
- **Solution**: 
  - Ensure the selected model has "question_generation" capability
  - Check if there's sufficient content/text
  - Increase timeout if generating many questions

**Issue**: High API costs
- **Solution**:
  - Adjust max_tokens parameter
  - Use more efficient models
  - Enable cost tracking and monitor usage

**Issue**: Slow responses
- **Solution**:
  - Check your internet connection
  - Try a different provider
  - Reduce content length or complexity

## Security Notes

1. **API Key Encryption**: All API keys are encrypted using Laravel's Crypt facade
2. **Environment Variables**: Never commit API keys to version control
3. **Access Control**: All AI routes require admin role authentication
4. **Rate Limiting**: Consider implementing rate limiting for API calls
5. **Cost Monitoring**: Monitor token usage and costs regularly

## Next Steps

After installation:

1. Test basic AI model connectivity
2. Create a test question generation
3. Verify all routes are accessible
4. Configure default models for each capability
5. Test blog post generation
6. Set up AI grading criteria if needed

## Additional Resources

- See `FILE_STRUCTURE.md` for detailed file descriptions
- See `README.md` for quick start guide
- Review the exported view files for UI reference

## Support

For issues or questions about the AI system:
1. Check the AI model configuration
2. Review service logs for errors
3. Test with different providers
4. Check Laravel logs: `php artisan log:tail`

---

**Last Updated**: 2026-01-23
**Version**: 1.0
