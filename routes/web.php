<?php

declare(strict_types=1);

use App\Http\Controllers\Frontend\AchievementConditionsController;
use App\Http\Controllers\Frontend\AchievementsController;
use App\Http\Controllers\Frontend\ArchetypesController;
use App\Http\Controllers\Frontend\BackgroundsController;
use App\Http\Controllers\Frontend\BehavioursController;
use App\Http\Controllers\Frontend\CurrenciesController;
use App\Http\Controllers\Frontend\DialoguesController;
use App\Http\Controllers\Frontend\EnvironmentsController;
use App\Http\Controllers\Frontend\EffectsController;
use App\Http\Controllers\Frontend\FoodController;
use App\Http\Controllers\Frontend\FormulasController;
use App\Http\Controllers\Frontend\I18NController;
use App\Http\Controllers\Frontend\ItemCategoriesController;
use App\Http\Controllers\Frontend\ItemModifiersController;
use App\Http\Controllers\Frontend\ItemRaritiesController;
use App\Http\Controllers\Frontend\ItemsController;
use App\Http\Controllers\Frontend\ItemSetsController;
use App\Http\Controllers\Frontend\ItemTypesController;
use App\Http\Controllers\Frontend\LootController;
use App\Http\Controllers\Frontend\BehaviourTreeController;
use App\Http\Controllers\Frontend\MasteriesController;
use App\Http\Controllers\Frontend\MissilesController;
use App\Http\Controllers\Frontend\PhrasesController;
use App\Http\Controllers\Frontend\PropertiesController;
use App\Http\Controllers\Frontend\RecipesController;
use App\Http\Controllers\Frontend\RelicsController;
use App\Http\Controllers\Frontend\RewardsController;
use App\Http\Controllers\Frontend\ScenariosController;
use App\Http\Controllers\Frontend\ScenesController;
use App\Http\Controllers\Frontend\SkillCategoriesController;
use App\Http\Controllers\Frontend\SkillsController;
use App\Http\Controllers\Frontend\SkillSetsController;
use App\Http\Controllers\Frontend\SkinsController;
use App\Http\Controllers\Frontend\TalentCategoriesController;
use App\Http\Controllers\Frontend\TalentGridController;
use App\Http\Controllers\Frontend\TalentsController;
use App\Http\Controllers\Frontend\TooltipsController;
use App\Http\Controllers\Frontend\UnitGroupsController;
use App\Http\Controllers\Frontend\UnitsController;
use App\Http\Controllers\Frontend\AttributesController;
use App\Http\Controllers\Frontend\ValidatorsController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\Frontend\VisionsController;
use App\Http\Controllers\Frontend\WeatherController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\DownloadController;
use Miroc\LaravelAdminer\AdminerAutologinController;

Route::any('/adminer', AdminerAutologinController::class . '@index');
Route::get('/', IndexController::class . '@index');

Route::get('/test', IndexController::class . '@test');
Route::get('/translation', IndexController::class . '@translation');

Route::get('/download', DownloadController::class . '@index');

Route::get('/import', IndexController::class . '@importIndex');
Route::post('/import', IndexController::class . '@importStore');

Route::post('/files', FilesController::class . '@upload');
Route::delete('/files/{file}', FilesController::class . '@delete');

Route::group(['prefix' => 'frontend'], function () {
    Route::resource('units', UnitsController::class);
    Route::resource('unit_groups', UnitGroupsController::class);
    Route::resource('scenes', ScenesController::class);
    Route::resource('achievements', AchievementsController::class);
    Route::resource('achievement_conditions', AchievementConditionsController::class);
    Route::resource('items', ItemsController::class);
    Route::resource('item_sets', ItemSetsController::class);
    Route::resource('item_types', ItemTypesController::class);
    Route::resource('item_rarities', ItemRaritiesController::class);
    Route::resource('item_categories', ItemCategoriesController::class);
    Route::resource('loot', LootController::class);
    Route::resource('skills', SkillsController::class);
    Route::resource('skill_sets', SkillSetsController::class);
    Route::resource('skill_categories', SkillCategoriesController::class);
    Route::resource('skins', SkinsController::class);
    Route::resource('effects', EffectsController::class);
    Route::resource('attributes', AttributesController::class);
    Route::resource('scenarios', ScenariosController::class);
    Route::resource('i18n', I18NController::class);
    Route::resource('behaviours', BehavioursController::class);
    Route::resource('environments', EnvironmentsController::class);
    Route::resource('properties', PropertiesController::class);
    Route::resource('currencies', CurrenciesController::class);
    Route::resource('talents', TalentsController::class);
    Route::resource('talent_categories', TalentCategoriesController::class);
    Route::resource('behaviour_trees', BehaviourTreeController::class);
    Route::resource('recipes', RecipesController::class);
    Route::resource('missiles', MissilesController::class);
    Route::resource('validators', ValidatorsController::class);
    Route::resource('rewards', RewardsController::class);
    Route::resource('archetypes', ArchetypesController::class);
    Route::resource('backgrounds', BackgroundsController::class);
    Route::resource('item_modifiers', ItemModifiersController::class);
    Route::resource('weather', WeatherController::class);
    Route::resource('formulas', FormulasController::class);
    Route::resource('relics', RelicsController::class);
    Route::resource('dialogues', DialoguesController::class);
    Route::resource('phrases', PhrasesController::class);
    Route::resource('masteries', MasteriesController::class);
    Route::resource('food', FoodController::class);
    Route::resource('visions', VisionsController::class);
});
