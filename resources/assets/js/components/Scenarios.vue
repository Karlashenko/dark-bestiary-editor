<template>
    <div>
        <div class="modal fade" id="map" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document" style="width: 1916px; text-align: center;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Map
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div>
                            <img src="/img/map.png" id="map-image" style="max-width: 100%;">
                            <img src="/img/marker.png" id="map-marker" style="position: absolute; left: 0px; top: 0px; width: 32px;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <list :elements="scenarios" :fields="['id', 'index', 'name_i18n.en', 'type', 'monster_level_min', 'monster_level_max']" v-model="selected"></list>
            </div>

            <div class="col-md-8">
                <div class="panel panel-default" v-if="selected">
                    <div class="panel-footer">
                        <div class="pull-left">
                            <button class="btn btn-primary btn-wide" @click="save(selected)"><span class="glyphicon glyphicon-floppy-disk"></span> Save</button>
                            <button class="btn btn-danger btn-wide" @click="destroy(selected)"><span class="glyphicon glyphicon-trash"></span> Delete</button>
                        </div>
                        <div class="pull-right">
                            <button class="btn btn-default" @click="duplicate(selected)"><span class="glyphicon glyphicon-duplicate"></span> Duplicate</button>
                            <button class="btn btn-default" @click="reset(selected)"><span class="glyphicon glyphicon-refresh"></span> Reset</button>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="panel-body">
                        <table class="form-table">
                            <tr>
                                <td>Id</td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control" v-model="selected.id" disabled />
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>Index</td>
                                <td>
                                    <div class="form-group">
                                        <input type="number" class="form-control" v-model="selected.index"/>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>Monster Level Min</td>
                                <td>
                                    <div class="form-group">
                                        <input type="number" class="form-control" v-model="selected.monster_level_min">
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>Monster Level Max</td>
                                <td>
                                    <div class="form-group">
                                        <input type="number" class="form-control" v-model="selected.monster_level_max">
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>Name I18N</td>
                                <td>
                                    <i18n-field v-model="selected.name_i18n_id"></i18n-field>
                                </td>
                            </tr>

                            <tr>
                                <td>Description I18N</td>
                                <td>
                                    <i18n-field v-model="selected.description_i18n_id"></i18n-field>
                                </td>
                            </tr>

                            <tr>
                                <td>Complete I18N</td>
                                <td>
                                    <i18n-field v-model="selected.complete_i18n_id"></i18n-field>
                                </td>
                            </tr>

                            <tr>
                                <td>Commentary I18N</td>
                                <td>
                                    <i18n-field v-model="selected.commentary_i18n_id"></i18n-field>
                                </td>
                            </tr>

                            <tr><td colspan="3"><hr></td></tr>

                            <tr>
                                <td>Type</td>
                                <td>
                                    <div class="form-group">
                                        <selectpicker v-model="selected.type"
                                                      :nullable="false"
                                                      :options="library.scenarioTypes">
                                        </selectpicker>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>Is Unlocked</td>
                                <td>
                                    <input type="checkbox" v-model="selected.is_unlocked">
                                </td>
                            </tr>

                            <tr>
                                <td>Is Hidden</td>
                                <td>
                                    <input type="checkbox" v-model="selected.is_hidden">
                                </td>
                            </tr>

                            <tr>
                                <td>Is Start</td>
                                <td>
                                    <input type="checkbox" v-model="selected.is_start">
                                </td>
                            </tr>

                            <tr>
                                <td>Is End</td>
                                <td>
                                    <input type="checkbox" v-model="selected.is_end">
                                </td>
                            </tr>

                            <tr>
                                <td>Is Disposable</td>
                                <td>
                                    <input type="checkbox" v-model="selected.is_disposable">
                                </td>
                            </tr>

                            <tr>
                                <td>Is Ascension</td>
                                <td>
                                    <input type="checkbox" v-model="selected.is_ascension">
                                </td>
                            </tr>

                            <tr><td colspan="3"><hr></td></tr>

                            <tr>
                                <td>Position</td>
                                <td>
                                    <p>
                                        <input type="number" class="form-control" v-model="selected.position_x" disabled>
                                    </p>

                                    <p>
                                        <input type="number" class="form-control" v-model="selected.position_y" disabled>
                                    </p>

                                    <p>
                                        <button id="map-button" type="button" class="btn btn-default" data-toggle="modal" data-target="#map" @click="updateMarker">Map</button>
                                    </p>
                                </td>
                            </tr>

                            <tr><td colspan="3"><hr></td></tr>

                            <tr>
                                <td>Gold</td>
                                <td>
                                    <p>
                                        <input type="number" class="form-control" v-model="selected.reward_gold">
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <td>Experience</td>
                                <td>
                                    <p>
                                        <input type="number" class="form-control" v-model="selected.reward_experience">
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <td>Rewards</td>
                                <td>
                                    <table class="form-table">
                                        <thead>
                                        <tr>
                                            <th style="width: 60%">Item</th>
                                            <th>Stack</th>
                                            <th>Choosable</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="item in selected.items">
                                            <td>
                                                <item-field v-model="item.id" :items="items"></item-field>
                                            </td>

                                            <td>
                                                <input type="number" class="form-control" v-model="item.pivot.stack_count">
                                            </td>

                                            <td style="text-align: center;">
                                                <input type="checkbox" class="form-check-input" v-model="item.pivot.is_choosable">
                                            </td>

                                            <td>
                                                <button class="btn btn-default" @click="removeItem(selected, item)">
                                                    <span class="glyphicon glyphicon-trash"></span>
                                                </button>
                                            </td>
                                        </tr>

                                        <tr style="height: 50px;">
                                            <td>
                                                <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addItem(selected)">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr><td colspan="3"><hr></td></tr>

                            <tr>
                                <td>Episodes</td>
                                <td>
                                    <div class="panel panel-default" v-for="episode in selected.episodes">
                                        <div class="panel-body">
                                            <table class="form-table">
                                                <tr>
                                                    <td>Label</td>
                                                    <td>
                                                        <input type="text" class="form-control" v-model="episode.label"/>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Environment</td>
                                                    <td>
                                                        <selectpicker v-model="episode.environment_id"
                                                                      :nullable="true"
                                                                      :options="environments"
                                                                      :valuecallback="(environment) => environment.id"
                                                                      :labelcallback="(environment) => environment.name_i18n.en">
                                                        </selectpicker>
                                                    </td>
                                                </tr>

                                                <tr><td colspan="3"><hr></td></tr>

                                                <tr>
                                                    <td>Scenes</td>
                                                    <td>
                                                        <table class="form-table">
                                                            <tbody>
                                                            <tr v-for="scene in episode.scenes">
                                                                <td>
                                                                    <selectpicker v-model="scene.id"
                                                                                  :nullable="false"
                                                                                  :options="scenes"
                                                                                  :valuecallback="(option) => option.id"
                                                                                  :labelcallback="(option) => option.label">
                                                                    </selectpicker>
                                                                </td>

                                                                <td>
                                                                    <button class="btn btn-default" @click="removeScene(episode, scene)">
                                                                        <span class="glyphicon glyphicon-trash"></span>
                                                                    </button>
                                                                </td>
                                                            </tr>

                                                            <tr style="height: 50px;">
                                                                <td>
                                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addScene(episode)">
                                                                        <span class="glyphicon glyphicon-plus"></span>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>

                                                <tr><td colspan="3"><hr></td></tr>

                                                <tr>
                                                    <td>Encounter</td>
                                                    <td>
                                                        <div class="form-group">
                                                            <selectpicker v-model="episode.encounter.type"
                                                                          :nullable="false"
                                                                          :options="encounters">
                                                            </selectpicker>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr><td colspan="3"><hr></td></tr>

                                                <tr>
                                                    <td>Start Phrase</td>
                                                    <td>
                                                        <div class="form-group">
                                                            <phrase-field v-model="episode.encounter.start_phrase_id" :phrases="phrases"></phrase-field>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Complete Phrase</td>
                                                    <td>
                                                        <div class="form-group">
                                                            <phrase-field v-model="episode.encounter.complete_phrase_id" :phrases="phrases"></phrase-field>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr><td colspan="3"><hr></td></tr>

                                                <tr>
                                                    <td>Phrases</td>
                                                    <td>
                                                        <table class="form-table">
                                                            <tbody>
                                                            <tr v-for="phrase in episode.encounter.phrases">
                                                                <td>
                                                                    <phrase-field v-model="phrase.id" :phrases="phrases"></phrase-field>
                                                                </td>

                                                                <td>
                                                                    <button class="btn btn-default" @click="removePhrase(episode, phrase)">
                                                                        <span class="glyphicon glyphicon-trash"></span>
                                                                    </button>
                                                                </td>
                                                            </tr>

                                                            <tr style="height: 50px;">
                                                                <td>
                                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addPhrase(episode)">
                                                                        <span class="glyphicon glyphicon-plus"></span>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>

                                                <tr><td colspan="3"><hr></td></tr>

                                                <tr>
                                                    <td>Unit Source</td>
                                                    <td>
                                                        <div class="form-group">
                                                            <selectpicker v-model="episode.encounter.unit_source_type"
                                                                          :nullable="true"
                                                                          :options="library.unitSourceTypes">
                                                            </selectpicker>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr :class="episode.encounter.unit_source_type === 'Environment' ? '' : 'hidden'">
                                                    <td>Unit Group Challenge Rating</td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" v-model="episode.encounter.unit_group_challenge_rating">
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr :class="episode.encounter.unit_source_type === 'Environment' ? '' : 'hidden'">
                                                    <td>Unit Group Environment</td>
                                                    <td>
                                                        <div class="form-group">
                                                            <selectpicker v-model="episode.encounter.unit_group_environment_id"
                                                                          :nullable="true"
                                                                          :options="environments"
                                                                          :valuecallback="(option) => option.id"
                                                                          :labelcallback="(option) => option.name_i18n.en">
                                                            </selectpicker>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr :class="episode.encounter.unit_source_type === 'Table' ? '' : 'hidden'">
                                                    <td>Unit count</td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" v-model="episode.encounter.unit_table.count">
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr :class="episode.encounter.unit_source_type === 'Table' ? '' : 'hidden'">
                                                    <td>Units</td>
                                                    <td>
                                                        <table class="form-table">
                                                            <thead>
                                                            <tr>
                                                                <th>Unit</th>
                                                                <th>Probability</th>
                                                                <th>Guaranteed</th>
                                                                <th>Unique</th>
                                                                <th>Enabled</th>
                                                                <th></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr v-for="unit in episode.encounter.unit_table.units">
                                                                <td>
                                                                    <selectpicker v-model="unit.unit_id"
                                                                                  :nullable="true"
                                                                                  :options="units"
                                                                                  :valuecallback="(option) => option.id"
                                                                                  :labelcallback="(option) => option.name_i18n.en">
                                                                    </selectpicker>
                                                                </td>

                                                                <td>
                                                                    <input type="number" class="form-control" v-model="unit.probability" :disabled="unit.is_guaranteed">
                                                                </td>

                                                                <td>
                                                                    <input style="width: 100%;" type="checkbox" class="form-checkbox" v-model="unit.is_guaranteed">
                                                                </td>

                                                                <td>
                                                                    <input style="width: 100%;" type="checkbox" class="form-checkbox" v-model="unit.is_unique">
                                                                </td>

                                                                <td>
                                                                    <input style="width: 100%;" type="checkbox" class="form-checkbox" v-model="unit.is_enabled">
                                                                </td>

                                                                <td>
                                                                    <button class="btn btn-default" @click="removeUnit(episode, unit)">
                                                                        <span class="glyphicon glyphicon-trash"></span>
                                                                    </button>
                                                                </td>
                                                            </tr>

                                                            <tr style="height: 50px;">
                                                                <td>
                                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addUnit(episode)">
                                                                        <span class="glyphicon glyphicon-plus"></span>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>

                                                <tr :class="episode.encounter.unit_source_type === 'UnitGroup' ? '' : 'hidden'">
                                                    <td>Unit Groups</td>
                                                    <td>
                                                        <table class="form-table">
                                                            <tbody>
                                                            <tr v-for="unitGroup in episode.encounter.unit_groups">
                                                                <td>
                                                                    <selectpicker v-model="unitGroup.id"
                                                                                  :nullable="true"
                                                                                  :options="unitGroups"
                                                                                  :valuecallback="(option) => option.id"
                                                                                  :labelcallback="(option) => option.label">
                                                                    </selectpicker>
                                                                </td>

                                                                <td>
                                                                    <button class="btn btn-default" @click="removeUnitGroup(episode, unitGroup)">
                                                                        <span class="glyphicon glyphicon-trash"></span>
                                                                    </button>
                                                                </td>
                                                            </tr>

                                                            <tr style="height: 50px;">
                                                                <td>
                                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addUnitGroup(episode)">
                                                                        <span class="glyphicon glyphicon-plus"></span>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>

                                        <div class="panel-footer">
                                            <div class="pull-right">
                                                <button class="btn btn-default" @click="moveEpisodeUp(selected, episode)">
                                                    <span class="glyphicon glyphicon-arrow-up"></span>
                                                </button>

                                                <button class="btn btn-default" @click="moveEpisodeDown(selected, episode)">
                                                    <span class="glyphicon glyphicon-arrow-down"></span>
                                                </button>

                                                <button class="btn btn-default" @click="removeEpisode(selected, episode)">
                                                    <span class="glyphicon glyphicon-trash"></span>
                                                </button>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>

                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addEpisode(selected)">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </button>
                                </td>
                            </tr>

                            <tr><td colspan="3"><hr></td></tr>

                            <tr>
                                <td>Unlocks Scenarios</td>
                                <td>
                                    <table style="width: 100%;">
                                        <tr v-for="scenario in selected.children">
                                            <td style="width: 100%;">
                                                <selectpicker v-model="scenario.id"
                                                              :nullable="true"
                                                              :options="scenarios"
                                                              :valuecallback="(option) => option.id"
                                                              :labelcallback="(option) => option.name_i18n.en">
                                                </selectpicker>
                                            </td>

                                            <td>
                                                <button class="btn btn-default" @click="removeScenario(selected, scenario)">
                                                    <span class="glyphicon glyphicon-trash"></span>
                                                </button>
                                            </td>
                                        </tr>

                                        <tr style="height: 50px;">
                                            <td>
                                                <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addScenario(selected)">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                </button>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="panel-footer">
                        <div class="pull-left">
                            <button class="btn btn-primary btn-wide" @click="save(selected)"><span class="glyphicon glyphicon-floppy-disk"></span> Save</button>
                            <button class="btn btn-danger btn-wide" @click="destroy(selected)"><span class="glyphicon glyphicon-trash"></span> Delete</button>
                        </div>
                        <div class="pull-right">
                            <button class="btn btn-default" @click="duplicate(selected)"><span class="glyphicon glyphicon-duplicate"></span> Duplicate</button>
                            <button class="btn btn-default" @click="reset(selected)"><span class="glyphicon glyphicon-refresh"></span> Reset</button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { Events } from './../events';

    export default {
        data() {
            return {
                loading: false,
                unitGroups: [],
                scenarios: [],
                items: [],
                units: [],
                scenes: [],
                phrases: [],
                environments: [],
                library: window.library,
                encounters: window.library.encounters,
                entrances: window.library.entrances,
                selected: undefined,
            };
        },

        mounted() {
            this.load();

            Events.$off();
            Events.$on('toolbar.load', this.load);
            Events.$on('toolbar.create', this.create);
            Events.$on('toolbar.save', this.saveAll);
            Events.$on('toolbar.download', this.download);
        },

        methods: {
            load: function () {
                window.axios.get('/frontend/scenarios').then(response => {
                    this.scenarios = response.data;
                    this.select(this.id === undefined ? this.scenarios[0] : this.scenarios.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/items').then(response => {
                    this.items = response.data;
                });

                window.axios.get('/frontend/phrases').then(response => {
                    this.phrases = response.data;
                });

                window.axios.get('/frontend/scenes').then(response => {
                    this.scenes = response.data;
                });

                window.axios.get('/frontend/units').then(response => {
                    this.units = response.data;
                });

                window.axios.get('/frontend/unit_groups').then(response => {
                    this.unitGroups = response.data;
                });

                window.axios.get('/frontend/environments').then(response => {
                    this.environments = response.data;
                });

                $("#map-image").click(event => {
                    let offset = $("#map-image").offset();
                    let x = (event.pageX - offset.left);
                    let y = (event.pageY - offset.top);

                    this.selected.position_x = x;
                    this.selected.position_y = y;

                    this.updateMarker();
                });
            },

            updateMarker: function () {
                let marker = document.getElementById("map-marker");
                marker.style.left = (this.selected.position_x + 8) + "px";
                marker.style.top = (this.selected.position_y - 8) + "px";
            },

            select: function (scenario) {
                this.selected = scenario;
                this.updateMarker();
            },

            create: function () {
                this.scenarios.push({
                    fresh: true,
                    name_i18n: {en: "scenario_"},
                    position_x: 0,
                    position_y: 0,
                    items: [],
                    episodes: []
                });

                this.select(this.scenarios[this.scenarios.length - 1]);
            },

            addScenario: function (item) {
                item.children.push({
                    id: this.scenarios[0] ? this.scenarios[0].id : undefined
                });
            },

            removeScenario: function (item, scenario) {
                let index = item.children.indexOf(scenario);
                item.children.splice(index, 1);
            },

            addScene: function (episode) {
                episode.scenes.push({
                    id: this.scenes[0] ? this.scenes[0].id : undefined
                });
            },

            removeScene: function (episode, scene) {
                let index = episode.scenes.indexOf(scene);
                episode.scenes.splice(index, 1);
            },

            duplicate: function (scenario) {
                let copy = Object.assign({}, scenario);
                copy.fresh = true;
                copy.id = '';
                copy.name = copy.name + " copy";

                this.scenarios.push(copy);
                this.select(copy);
            },

            reset: function (scenario) {
                if (scenario.fresh) {
                    return;
                }

                let index = this.scenarios.indexOf(scenario);

                window.axios.get('/frontend/scenarios/' + scenario.id).then(response => {
                    this.scenarios[index] = response.data;
                    this.select(this.scenarios[index]);
                });
            },

            destroy: function (scenario) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.scenarios.indexOf(scenario);

                if (scenario.fresh) {
                    this.scenarios.splice(index, 1);
                    this.select(this.scenarios[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/scenarios/' + scenario.id).then(response => {
                    this.scenarios.splice(index, 1);
                    this.select(this.scenarios[index <= 0 ? 0 : index - 1]);
                });
            },

            addItem: function (scenario) {
                scenario.items.push({
                    id: this.items[0] ? this.items[0].id : undefined,
                    pivot: {
                        is_choosable: false,
                        stack_count: 0,
                    }
                });
            },

            removeItem: function (scenario, item) {
                let index = scenario.items.indexOf(item);
                scenario.items.splice(index, 1);
            },

            addEpisode: function (scenario) {
                scenario.episodes.push({
                    units: [],
                    scenes: [],
                    encounter: {
                        unit_table: {
                            units: []
                        },
                        unit_groups: []
                    },
                });
            },

            removeEpisode: function (scenario, episode) {
                let index = scenario.episodes.indexOf(episode);
                scenario.episodes.splice(index, 1);
            },

            moveEpisodeUp: function (scenario, episode) {
                let index = scenario.episodes.indexOf(episode);

                if (index === -1 || index === 0) {
                    return;
                }

                scenario.episodes[index] = scenario.episodes[index - 1];
                scenario.episodes[index - 1] = episode;

                this.$forceUpdate();
            },

            moveEpisodeDown: function (scenario, episode) {
                let index = scenario.episodes.indexOf(episode);

                if (index === -1 || index === scenario.episodes.length) {
                    return;
                }

                scenario.episodes[index] = scenario.episodes[index + 1];
                scenario.episodes[index + 1] = episode;

                this.$forceUpdate();
            },

            addUnit: function (episode) {
                episode.encounter.unit_table.units.push({
                    unit_id: this.units[0] ? this.units[0].id : undefined,
                    is_enabled: true
                });
            },

            removeUnit: function (episode, unit) {
                let index = episode.encounter.unit_table.units.indexOf(unit);
                episode.encounter.unit_table.units.splice(index, 1);
            },

            addPhrase: function (episode) {
                episode.encounter.phrases.push({
                    id: this.phrases[0] ? this.phrases[0].id : undefined,
                });
            },

            removePhrase: function (episode, phrase) {
                let index = episode.encounter.phrases.indexOf(phrase);
                episode.encounter.phrases.splice(index, 1);
            },

            addUnitGroup: function (episode) {
                episode.encounter.unit_groups.push({
                    id: this.unitGroups[0] ? this.unitGroups[0].id : undefined,
                });
            },

            removeUnitGroup: function (episode, unitGroup) {
                let index = episode.encounter.unit_groups.indexOf(unitGroup);
                episode.encounter.unit_groups.splice(index, 1);
            },

            save: function (scenario) {
                let index = this.scenarios.indexOf(scenario);

                if (scenario.fresh) {
                    window.axios.post('/frontend/scenarios', scenario).then(response => {
                        this.scenarios[index] = response.data;
                        this.select(this.scenarios[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/scenarios/' + scenario.id, scenario).then(response => {
                    this.scenarios[index] = response.data;
                    this.select(this.scenarios[index]);
                });
            },

            saveAll: function () {
                for (let key in this.scenarios) {
                    let scenario = this.scenarios[key];

                    if (scenario.fresh) {
                        window.axios.post('/frontend/scenarios', scenario);
                        continue;
                    }

                    window.axios.put('/frontend/scenarios/' + scenario.id, scenario);
                }

                this.load();
            }
        }
    };
</script>
