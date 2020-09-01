<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ApiController;

Route::get('units', ApiController::class . '@units');
Route::get('archetypes', ApiController::class . '@archetypes');
Route::get('scenes', ApiController::class . '@scenes');
Route::get('loot', ApiController::class . '@loot');
Route::get('achievements', ApiController::class . '@achievements');
Route::get('behaviours', ApiController::class . '@behaviours');
Route::get('missiles', ApiController::class . '@missiles');
Route::get('attributes', ApiController::class . '@attributes');
Route::get('properties', ApiController::class . '@properties');
Route::get('scenarios', ApiController::class . '@scenarios');
Route::get('environments', ApiController::class . '@environments');
Route::get('effects', ApiController::class . '@effects');
Route::get('skills', ApiController::class . '@skills');
Route::get('skill_sets', ApiController::class . '@skillSets');
Route::get('skill_categories', ApiController::class . '@skillCategories');
Route::get('skins', ApiController::class . '@skins');
Route::get('items', ApiController::class . '@items');
Route::get('item_sets', ApiController::class . '@itemSets');
Route::get('item_types', ApiController::class . '@itemTypes');
Route::get('item_categories', ApiController::class . '@itemCategories');
Route::get('item_rarities', ApiController::class . '@itemRarities');
Route::get('talents', ApiController::class . '@talents');
Route::get('talent_categories', ApiController::class . '@talentCategories');
Route::get('currencies', ApiController::class . '@currencies');
Route::get('ai', ApiController::class . '@ai');
Route::get('rewards', ApiController::class . '@rewards');
Route::get('recipes', ApiController::class . '@recipes');
Route::get('validators', ApiController::class . '@validators');
Route::get('backgrounds', ApiController::class . '@backgrounds');
Route::get('item_modifiers', ApiController::class . '@itemModifiers');
Route::get('unit_groups', ApiController::class . '@unitGroups');
Route::get('relics', ApiController::class . '@relics');
Route::get('dialogues', ApiController::class . '@dialogues');
Route::get('phrases', ApiController::class . '@phrases');
Route::get('masteries', ApiController::class . '@masteries');
Route::get('food', ApiController::class . '@food');
Route::get('i18n', ApiController::class . '@i18n');
Route::get('i18n/ru', ApiController::class . '@i18nRu');
Route::get('i18n/en', ApiController::class . '@i18nEn');
