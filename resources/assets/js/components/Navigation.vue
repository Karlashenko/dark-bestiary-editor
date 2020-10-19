<template>
    <div class="btn-group" role="group" style="margin-left: 20px; display: inline-block;">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Units
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu">
                <li>
                    <router-link to="/units">Units</router-link>
                </li>

                <li>
                    <router-link to="/archetypes">Archetypes</router-link>
                </li>

                <li>
                    <router-link to="/backgrounds">Backgrounds</router-link>
                </li>

                <li>
                    <router-link to="/unit_groups">Groups</router-link>
                </li>
            </ul>
        </div>

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Skills
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu">
                <li>
                    <router-link to="/skills">Skills</router-link>
                </li>

                <li>
                    <router-link to="/skill_sets">Sets</router-link>
                </li>

                <li>
                    <router-link to="/skill_categories">Categories</router-link>
                </li>

                <li>
                    <router-link to="/behaviours">Behaviours</router-link>
                </li>

                <li>
                    <router-link to="/effects">Effects</router-link>
                </li>

                <li>
                    <router-link to="/formulas">Formulas</router-link>
                </li>

                <li>
                    <router-link to="/validators">Validators</router-link>
                </li>

                <li>
                    <router-link to="/missiles">Missiles</router-link>
                </li>
            </ul>
        </div>

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Items
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu">
                <li>
                    <router-link to="/items">Items</router-link>
                </li>

                <li>
                    <router-link to="/relics">Relics</router-link>
                </li>

                <li>
                    <router-link to="/item_modifiers">Modifiers</router-link>
                </li>

                <li>
                    <router-link to="/item_sets">Sets</router-link>
                </li>

                <li>
                    <router-link to="/item_types">Types</router-link>
                </li>

                <li>
                    <router-link to="/item_categories">Categories</router-link>
                </li>

                <li>
                    <router-link to="/item_rarities">Rarities</router-link>
                </li>

                <li>
                    <router-link to="/recipes">Recipes</router-link>
                </li>

                <li>
                    <router-link to="/skins">Skins</router-link>
                </li>

                <li>
                    <router-link to="/loot">Loot</router-link>
                </li>
            </ul>
        </div>

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Talents
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu">
                <li>
                    <router-link to="/talents">Talents</router-link>
                </li>

                <li>
                    <router-link to="/talent_categories">Talent Categories</router-link>
                </li>

                <li>
                    <router-link to="/masteries">Masteries</router-link>
                </li>
            </ul>
        </div>

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Achievements
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu">
                <li>
                    <router-link to="/achievements">Achievements</router-link>
                </li>

                <li>
                    <router-link to="/achievement_conditions">Conditions</router-link>
                </li>
            </ul>
        </div>

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Scenarios
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu">
                <li>
                    <router-link to="/scenarios">Scenarios</router-link>
                </li>

                <li>
                    <router-link to="/dialogues">Dialogues</router-link>
                </li>

                <li>
                    <router-link to="/phrases">Phrases</router-link>
                </li>

                <li>
                    <router-link to="/environments">Environments</router-link>
                </li>
                <li>
                    <router-link to="/weather">Weather</router-link>
                </li>

                <li>
                    <router-link to="/scenes">Scenes</router-link>
                </li>

                <li>
                    <router-link to="/rewards">Rewards</router-link>
                </li>

                <li>
                    <router-link to="/visions">Visions</router-link>
                </li>
            </ul>
        </div>

        <router-link tag="a" class="btn btn-default" to="/attributes">Attributes</router-link>
        <router-link tag="a" class="btn btn-default" to="/properties">Properties</router-link>
        <router-link tag="a" class="btn btn-default" to="/currencies">Currencies</router-link>
        <router-link tag="a" class="btn btn-default" to="/food">Food</router-link>
        <router-link tag="a" class="btn btn-default" to="/ai">AI</router-link>
        <router-link tag="a" class="btn btn-default" to="/i18n">I18N</router-link>
    </div>
</template>
