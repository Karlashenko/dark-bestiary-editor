<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="achievements" :fields="['id', 'index', 'name_i18n.en']" :icon="'icon'" v-model="selected"></list>
                </div>

                <div class="col-md-9">
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
                                            <input type="text" class="form-control" v-model="selected.id" disabled>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Index</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.index">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Is Enabled</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.is_enabled"/>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Steam API Key</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.steam_api_key">
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Name I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.name_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Description I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.description_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Icon</td>
                                    <td>
                                        <icon-field v-model="selected.icon"></icon-field>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.type" :options="window.library.achievementTypes"></selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Conditions</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tr v-for="condition in selected.conditions">
                                                <td>
                                                    <div class="form-group">
                                                        <selectpicker v-model="condition.id"
                                                                      :nullable="true"
                                                                      :options="conditions"
                                                                      :valuecallback="(element) => element.id"
                                                                      :labelcallback="(element) => element.label">
                                                        </selectpicker>
                                                    </div>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeCondition(selected, category)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="2">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addCondition(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Level</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.level">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Unit</td>
                                    <td>
                                        <unit-field v-model="selected.unit_id" :units="units"></unit-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Item</td>
                                    <td>
                                        <item-field v-model="selected.item_id" :items="items"></item-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Scenario</td>
                                    <td>
                                        <selectpicker v-model="selected.scenario_id"
                                                      :nullable="true"
                                                      :options="scenarios"
                                                      :valuecallback="(element) => element.id"
                                                      :labelcallback="(element) => element.name_i18n.en">
                                        </selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Required Quantity</td>
                                    <td>
                                        <div class="form-group">
                                            <input class="form-control" type="number" v-model="selected.required_quantity">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Points</td>
                                    <td>
                                        <div class="form-group">
                                            <input class="form-control" type="number" v-model="selected.points">
                                        </div>
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
    </div>
</template>

<script>
    import {Events} from './../events';

    export default {
        data() {
            return {
                achievements: [],
                conditions: [],
                scenarios: [],
                items: [],
                units: [],
                window: window,
                selected: undefined
            };
        },

        mounted() {
            Events.$off();
            Events.$on('toolbar.load', this.load);
            Events.$on('toolbar.create', this.create);
            Events.$on('toolbar.save', this.saveAll);
            Events.$on('toolbar.download', this.download);

            this.load();
        },

        methods: {
            load: function () {
                window.axios.get('/frontend/achievements').then(response => {
                    this.achievements = response.data;
                    this.select(this.id === undefined ? this.achievements[0] : this.achievements.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/units').then(response => {
                    this.units = response.data;
                });

                window.axios.get('/frontend/scenarios').then(response => {
                    this.scenarios = response.data;
                });

                window.axios.get('/frontend/items').then(response => {
                    this.items = response.data;
                });

                window.axios.get('/frontend/achievement_conditions').then(response => {
                    this.conditions = response.data;
                });
            },

            addCondition: function (achievement) {
                achievement.conditions.push({
                    id: this.conditions[0] ? this.conditions[0].id : undefined
                });
            },

            removeCondition: function (achievement, condition) {
                let index = achievement.conditions.indexOf(condition);
                achievement.conditions.splice(index, 1);
            },

            create: function () {
                this.achievements.push({
                    fresh: true,
                    name_i18n: {en: "achievement_"}
                });

                this.select(this.achievements[this.achievements.length - 1]);
            },

            select: function (achievement) {
                this.selected = achievement;
            },

            duplicate: function (achievement) {
                let copy = Object.assign({}, achievement);
                copy.fresh = true;
                this.achievements.push(copy);
                this.select(copy);
            },

            reset: function (achievement) {
                if (achievement.fresh) {
                    return;
                }

                let index = this.achievements.indexOf(achievement);

                window.axios.get('/frontend/achievements/' + achievement.id).then(response => {
                    this.achievements[index] = response.data;
                    this.select(this.achievements[index]);
                });
            },

            save: function (achievement) {
                let index = this.achievements.indexOf(achievement);

                if (achievement.fresh) {
                    window.axios.post('/frontend/achievements', achievement).then(response => {
                        this.achievements[index] = response.data;
                        this.select(this.achievements[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/achievements/' + achievement.id, achievement).then(response => {
                    this.achievements[index] = response.data;
                    this.select(this.achievements[index]);
                });
            },

            destroy: function (achievement) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.achievements.indexOf(achievement);

                if (achievement.fresh) {
                    this.achievements.splice(index, 1);
                    this.select(this.achievements[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/achievements/' + achievement.id).then(() => {
                    this.achievements.splice(index, 1);
                    this.select(this.achievements[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.achievements) {
                    let achievement = this.achievements[key];

                    if (achievement.fresh) {
                        window.axios.post('/frontend/achievements', achievement).then(response => {
                            this.achievements[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/achievements/' + achievement.id, achievement).then(response => {
                        this.achievements[key] = response.data;
                    });
                }
            }
        }
    };
</script>
