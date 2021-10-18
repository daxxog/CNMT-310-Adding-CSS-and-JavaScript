<svelte:head>
    <title>Week 7: JavaScript Validation and CSS</title>
</svelte:head>

<style>
.row {
    font-family: Monospace;
}
div.error {
    color: red;
}
span.error-header {
    font-size: 120%;
    font-weight: bold;
}
label.bad-label {
    color: red;
}
input.bad-input {
    border-color: red;
}
span.thanks {
    font-size: 120%;
    font-weight: bold;
    color: blue;
}
</style>

<script>
    import * as EmailValidator from 'email-validator';


    const elementValues = {};

    // string constants exported from python
    const ASCII_LOWERCASE = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"],
          ASCII_UPPERCASE = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"],
          DIGITS = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"],
          PUNCTUATION = ["!", "\"", "#", "$", "%", "&", "'", "(", ")", "*", "+", ",", "-", ".", "/", ":", ";", "<", "=", ">", "?", "@", "[", "\\", "]", "^", "_", "`", "{", "|", "}", "~"];

    const CHARTYPE_ASCII_LOWERCASE = 0,
          CHARTYPE_ASCII_UPPERCASE = 1,
          CHARTYPE_DIGITS = 2,
          CHARTYPE_PUNCTUATION = 3,
          CHARTYPE_OTHER = 4;

    const DEFAULT_ERROR_MESSAGE = 'Something unexpected happened while processing your request. Please contact support@example.com for assistance.';

    class ErrorHandler {
        constructor() {
            this.errors = [];
            this.markedErrors = {};
        }

        get hasError() {
            return this.errors.length !== 0;
        }

        async addError(errorMessage) {
            this.errors.push(errorMessage);
        }

        isMarked(elementName) {
            return this.markedErrors[elementName] ?? false;
        }

        async markError(elementName) {
            this.markedErrors[elementName] = true;
        }
    }

    let registrationErrorHandler = new ErrorHandler();

    class Validator {
        constructor(kwargs) {
            this.elementName = kwargs['name'];
            this.validateFunction = kwargs['validate'];
            this.errorMessage = kwargs['errorMessage'];
        }

        async validate(errorHandler) {
            const validationResult = await this.validateFunction(elementValues[this.elementName] ?? '');

            if(validationResult === false) {
                await errorHandler.markError(this.elementName);
                errorHandler.addError(this.errorMessage);
            }

            return validationResult;
        }
    }

    const findCharType = (char) => {
        if(ASCII_LOWERCASE.includes(char)) {
            return CHARTYPE_ASCII_LOWERCASE;
        } else if (ASCII_UPPERCASE.includes(char)) {
            return CHARTYPE_ASCII_UPPERCASE;
        } else if (DIGITS.includes(char)) {
            return CHARTYPE_DIGITS;
        } else if (PUNCTUATION.includes(char)) {
            return CHARTYPE_PUNCTUATION;
        } else {
            return CHARTYPE_OTHER;
        }
    };

    const validators = [
        {
            "name": 'username',
            "errorMessage": 'Your username must be atleast three characters long.',
            "validate": async username => username.length >= 3
        },
        {
            "name": 'emailAddress',
            "errorMessage": 'You must enter a valid email address.',
            "validate": async emailAddress => EmailValidator.validate(emailAddress)
        },
        {
            "name": 'password',
            "errorMessage": 'Password must be atleast 32 characters.',
            "validate": async password => password.length >= 32
        },
        {
            "name": 'password',
            "errorMessage": 'Password must not repeat a character type (Number, ABC, abc, Special, Other) e.g. "ab" is an invalid sequence but "aB" is a valid sequence.',
            "validate": async password => {
                if (password.length === 0) {
                    return false;
                } else {
                    return password.split('').map(findCharType).reduce( (p, c, i, a) => {
                        if(i > 1) {
                            return p && (a[i - 1] !== c);
                        } else {
                            return p !== c;
                        }
                    });
                }
            }
        },
        {
            "name": 'confirmPassword',
            "errorMessage": 'Passwords must match.',
            "validate": async confirmPassword => confirmPassword === elementValues.password
        }
    ].map( kwargs => new Validator(kwargs) );

    let formSubmitted = false;

    const handleSubmit = async e => {
        const errorHandler = new ErrorHandler();
        const valid = (await Promise.all(
            validators.map(
                validator => validator.validate(errorHandler)
            )
        )).reduce( (p, c) => p && c );

        if(valid === true) {
            const formData = new FormData();
            for (const index in elementValues) {
                formData.append(index, elementValues[index]);
            }

            const res = await fetch('./api/process-form.php', {
                method: 'POST',
                body: formData
            }).then( res => res.json() );

            if(typeof res === 'object') {
                errorHandler.addError(res['display_error'] ?? DEFAULT_ERROR_MESSAGE);
            } else {
                switch (res) {
                    case 'good request':
                        formSubmitted = true;
                        break;
                    case 'bad request':
                    default:
                        errorHandler.addError(DEFAULT_ERROR_MESSAGE);
                }
            }
        }

        registrationErrorHandler = errorHandler;
    };
</script>


<div class="row">
    <div class="twelve columns">
        <br>
    </div>
</div>
<div class="row">
    <div class="twelve columns">
        <h1>User Registration</h1>
    </div>
</div>
<div class="row">
    <div class="twelve columns">
        {#if registrationErrorHandler.hasError}
            <div class="error">
                <span class="error-header">There were some problems with your registration.</span>
                <br><br>
                <ul>
                    {#each registrationErrorHandler.errors as error}
                        <li>{error}</li>
                    {/each}
                </ul>
            </div>
        {/if}
    </div>
</div>
{#if !formSubmitted}
    <form on:submit|preventDefault={handleSubmit}>
        <div class="row">
            <div class="six columns">
                <label for="username" class:bad-label="{registrationErrorHandler.isMarked('username')}">Username:</label><br>
                <input class="u-full-width" class:bad-input="{registrationErrorHandler.isMarked('username')}" bind:value="{elementValues.username}" type="text" id="username" name="username"><br>
            </div>
            <div class="six columns">
                <label for="emailAddress" class:bad-label="{registrationErrorHandler.isMarked('emailAddress')}">Email address:</label><br>
                <input class="u-full-width" class:bad-input="{registrationErrorHandler.isMarked('emailAddress')}" bind:value="{elementValues.emailAddress}" type="text" id="emailAddress" name="emailAddress"><br>
            </div>
        </div>
        <div class="row">
            <div class="six columns">
                <label for="password" class:bad-label="{registrationErrorHandler.isMarked('password')}">Password:</label><br>
                <input class="u-full-width" class:bad-input="{registrationErrorHandler.isMarked('password')}" bind:value="{elementValues.password}" type="password" id="password" name="password"><br>
            </div>
            <div class="six columns">
                <label for="confirmPassword" class:bad-label="{registrationErrorHandler.isMarked('confirmPassword')}">Confirm password:</label><br>
                <input class="u-full-width" class:bad-input="{registrationErrorHandler.isMarked('confirmPassword')}" bind:value="{elementValues.confirmPassword}" type="password" id="confirmPassword" name="confirmPassword"><br>
            </div>
        </div>
        <div class="row">
            <div class="six columns">
                &nbsp;
            </div>
            <div class="six columns">
                <button class="button-primary" type="submit" value="register">Register</button> <br>
            </div>
        </div>
    </form>
{:else}
    <div class="row">
        <div class="twelve columns">
            <span class="thanks">Thank you for registering!</span>
        </div>
    </div>
{/if}
