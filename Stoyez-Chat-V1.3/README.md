# Stoyez-chat-PHP V1.3 - September 28, 2020 20:01 MDT

    A streamlined easy to setup and modify PHP chat for websites, including Tor websites. All this chat uses
    is PHP and HTML. If you find any bugs, please feel free to let me know and I will fix them in future 
    versions; Have any ideas for what I should add? Feel free to let me know and I'll add them to my todo list.

### General Information:

    This is a PHP Chat lightly based on LeChat created a while ago based in the PERL programming language; 
    I've created Stoyez-Chat to be easily setup with little to no Admin involvment in editing files to get 
    it to work. All the files that need to be changed to get it functioning are included in the setup.php 
    page. I've also created this chat to make the scripting easily navigatable and readable so that anyone 
    wanting to add in their own features to make the chat fit their needs easy and quick with little time 
    needed to learn how I built it.

### Features Implemented Now:

    Optimized for TOR
    No JavaScript needed
    Guests, Members, Mods, Special and Admin
    Admin Registration of Guests
    Public, member, waiting room, mod accept required (still in progress), moderator and admin only chats
    Clean selected messages for admins
    Time format options for either 12 hour or 24 hour clocks
	Kick chatters
	Clean the whole room
    And more

### features on their way soon.
    
    Captcha
    Multiple languages
    Private messages
	Autologout when inactive for some time
    Change background and refresh rate in a profile tab.
    Image embedding
    Notes for admins and moderators   

### Installation Instructions:

    1. You'll need to have a MYSQL server installed with apache2, after you have a fresh install of 
    MYSQL create a database(this will be automated in the next version), goto (yourwebsite)/(chatdir)/setup.php.

    2. Once you're on the setup.php page you can put in your Database host url, the username and password 
    for the database, and you can choose a database that you want the the chat files to be stored in, the 
    setup.php will auto generate the database and all the tables needed for the chat to function.

    3. Select a Admin username and password you'll want to use to administrate your chat with.
