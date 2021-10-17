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
</style>

<script>
    class ErrorHandler {
    }

    class Validator {
        constructor(kwargs) {
            this.elementName = kwargs['name'];
            this.validateFunction = kwargs['validate'];
        }

        async validate() {
            console.log('validate called for ' + this.elementName);
            return await this.validateFunction();
        }
    }

    const validators = [
        {
            "name": 'username',
            "validate": async username => {
                return true;
            }
        }
    ].map( kwargs => new Validator(kwargs) );

    const handleSubmit = async e => {
        console.log('hi',e, validators);
        const valid = await Promise.all(
            validators.map(
                validator => validator.validate()
            )
        );
        console.log(valid);
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
        <div class="error">
            <span class="error-header">There were some problems with your registration.</span>
            <br><br>
            <ul>
                <li>Problem 1</li>
                <li>Problem 2</li>
            </ul>
        </div>
    </div>
</div>
<form on:submit|preventDefault={handleSubmit}>
    <div class="row">
        <div class="six columns">
            <label for="username">Username:</label><br>
            <input class="u-full-width" type="text" id="username" name="username"><br>
        </div>
        <div class="six columns">
            <label for="email-address">Email address:</label><br>
            <input class="u-full-width" type="text" id="email-address" name="email-address"><br>
        </div>
    </div>
    <div class="row">
        <div class="six columns">
            <label for="password">Password:</label><br>
            <input class="u-full-width" type="password" id="password" name="password"><br>
        </div>
        <div class="six columns">
            <label for="confirm-password">Confirm password:</label><br>
            <input class="u-full-width" type="password" id="confirm-password" name="confirm-password"><br>
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
