<p align="center">
  <a href='https://weremote.net'>
    <img src="https://weremote.net/wp-content/uploads/2021/04/Logo-WR.svg" width="300" />
  </a>
</p>
<br />

## DescriptionWordPress Challenge is an admission test for developers specializing in WordPress (HTML, CSS, JavaScript, and PHP).

# Steps
1. Create a fork and provide the link to your personal repository. We will evaluate the test in your personal repository.
2. Follow the documentation for the challenges outlined in this link.
3. Use good programming practices.
4. Use the native WordPress graphical framework for building elements.

# Best Practices and Suggestions
1. Write readable and well-structured code.
2. Use English for code and comments.

# Deadline
1. Seven (7) days from the email sending the Tech Challenge. The exact date is provided in the email.
2. Notify completion via email and send only the repository URL for evaluation.
3. Always use the "Reply to All" option in the email.

# Comments
* The deployment of this technical test can be viewed at https://sebastian-trujillo.me/wordpress-test/ using the credentials provided by email.
* The configuration panel is located at settings/Etherion Kit Tools.
* Spanish language support has been included, and the translation files are located in the lang folder.

## Challenge 1: Classification of Broken Links
* The logic for this challenge was handled with functions in functions/core, supported by the custom Link_Status_Table class for the custom table.
* The initialization of the cron job that monitors the links is configured when the plugin is activated and removed when it is deactivated.
* The link analysis cron job is configured with a custom interval of 30 minutes in hooks/core -> custom_cron_schedules.
* The processing of links in the posts is done with the function functions/core -> find_broken_links, which takes blocks of 100 posts, giving priority to posts that have never been analyzed or to those that have not been analyzed for more than 4 days to prevent reanalysis.
* The configuration panel includes a table with the consolidated grouped links from different posts and their corresponding error status.

## Challenge 2: Posts API
* The logic for this challenge was oriented towards classes, with the creation of the Posts_API class responsible for registering routes and storing CRUD logic. In more complex projects, it would be a good practice to separate layers, distinguishing between the controller and the logical layer where data manipulation occurs.
* In the docs folder, you will find the JSON file of a Postman collection with all the endpoints to be tested, as well as the Swagger documentation YAML file that was requested.
* The configuration panel includes an input to save the authentication key used by the API to validate access to the data.
* For this example, the endpoints that manipulate POST, PUT, DELETE data were limited, while the endpoints that only read GET data were left open for access.