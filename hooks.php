<?php

hook::add('user_new','message::sendShareSteps');
hook::add('user_new','message::sendMakeMicroguides');
hook::add('story_remove', 'microguide::removeStoryHandler');
hook::add('user_username_changed', 'story::usernameChanged');
hook::add('user_username_changed', 'microguide::usernameChanged');
hook::add('user_username_changed', 'session::usernameChanged');
