<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/
Artisan::add(new NRQueueListen);
Artisan::add(new CreateUSLessonFromEN);
Artisan::add(new BatchAudioConvert);
Artisan::add(new DirAudioConvert);
Artisan::add(new DialogueDebug);
Artisan::add(new CheckLessonSort);
Artisan::add(new CreateLessonKey);
Artisan::add(new CreateBookTreeForIOS);
Artisan::add(new HandlerWordXML);
Artisan::add(new ParseNewWord);
Artisan::add(new GrabDictMp3);
Artisan::add(new BatchModifyDictAudioUrl);
Artisan::add(new CreateDictJson);
Artisan::add(new CreateNewWordJson);
Artisan::add(new CreateAudioTitle);
Artisan::add(new NewWordNotInDict);
Artisan::add(new SetWeixinIndustry);
Artisan::add(new SetWeixinMenu);
Artisan::add(new ReadQueueListen);

