<template>
    <div>
        <div>
            <div v-if="loading" class="loading"></div>

            <div class="row" v-if="!loading">
                <div class="col-md-4">
                    <list :elements="units" :fields="['id', 'name_i18n.en|label', 'archetype.name', 'suffix', 'challenge_rating', getBehavioursInfo, getQuestMarker]" v-model="selected" :filters="getFilters()"></list>
                </div>

                <div class="col-md-8">
                    <ul class="nav nav-tabs">
                        <li role="presentation" @click="tabIndex = 0" :class="tabIndex === 0 ? 'active' : ''"><a href="#">General</a></li>
                        <li role="presentation" @click="tabIndex = 1" :class="tabIndex === 1 ? 'active' : ''"><a href="#">Gallery</a></li>
                    </ul>

                    <div class="panel panel-default panel-tabs" v-if="selected">
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
                            <table class="form-table" v-if="tabIndex === 0">
                                <tr>
                                    <td>Id</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.id" disabled>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Label</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.label">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Suffix</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.suffix">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Name I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.name_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Description I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.description_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Challenge Rating</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.challenge_rating">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Archetype</td>
                                    <td>
                                        <selectpicker v-model="selected.archetype_id"
                                                      :nullable="true"
                                                      :options="archetypes"
                                                      :valuecallback="(archetype) => archetype.id"
                                                      :labelcallback="(archetype) => archetype.name">
                                        </selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Armor Sound Type</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.armor_sound_type" :options="library.armorSoundTypes"></selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Model</td>
                                    <td>
                                        <prefab-field v-model="selected.model"></prefab-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Environment</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.environment_id"
                                                          :nullable="true"
                                                          :options="environments"
                                                          :valuecallback="(environment) => environment.id"
                                                          :labelcallback="(environment) => environment.name_i18n.en">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>AI</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.behaviour_tree_id"
                                                          :nullable="true"
                                                          :options="behaviourTrees"
                                                          :valuecallback="(tree) => tree.id"
                                                          :labelcallback="(tree) => tree.name">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Skills</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tr v-for="skill in selected.skills">
                                                <td style="width: 90%;">
                                                    <skill-field v-model="skill.id" :skills="skills"></skill-field>
                                                </td>

                                                <td style="padding-left: 4px; vertical-align: top; white-space: nowrap;">
                                                    <button class="btn btn-default" @click="skillMoveUp(selected, skill)">
                                                        <span class="glyphicon glyphicon-arrow-up"></span>
                                                    </button>

                                                    <button class="btn btn-default" @click="skillMoveDown(selected, skill)">
                                                        <span class="glyphicon glyphicon-arrow-down"></span>
                                                    </button>

                                                    <button class="btn btn-default" @click="removeSkill(selected, skill)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addSkill(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Behaviours</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tr v-for="behaviour in selected.behaviours">
                                                <td style="width: 90%;">
                                                    <behaviour-field v-model="behaviour.id" :behaviours="behaviours"></behaviour-field>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeBehaviour(selected, behaviour)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addBehaviour(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Equipment</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tr v-for="item in selected.items">
                                                <td style="width: 90%;">
                                                    <item-field v-model="item.id" :items="items"></item-field>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeItem(selected, item)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addItem(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Loot</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tr v-for="item in selected.loot">
                                                <td style="width: 90%;">
                                                    <selectpicker v-model="item.id"
                                                                  :nullable="false"
                                                                  :options="loot"
                                                                  :valuecallback="(loot) => loot.id"
                                                                  :labelcallback="(loot) => loot.name">
                                                    </selectpicker>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeLoot(selected, item)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addLoot(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Flags</td>
                                    <td>
                                        <flags v-model="selected.flags" :flags="library.unitFlags"></flags>
                                    </td>
                                </tr>
                            </table>

                            <table class="form-table" v-if="tabIndex === 1">
                                <image-gallery v-if="!selected.fresh" v-model="selected.files"></image-gallery>
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
    import {Events} from "./../events";

    export default {
        props: ["id"],

        data() {
            return {
                loading: true,
                tabIndex: 0,
                units: [],
                loot: [],
                skills: [],
                items: [],
                archetypes: [],
                attributes: [],
                properties: [],
                environments: [],
                behaviours: [],
                window: window,
                library: window.library,
                behaviourTrees: [],
                search: "",
                selected: undefined
            };
        },

        mounted() {
            this.load();

            Events.$off();
            Events.$on("toolbar.load", this.load);
            Events.$on("toolbar.create", this.create);
            Events.$on("toolbar.save", this.saveAll);
            Events.$on("toolbar.download", this.download);
        },

        updated() {
            $(this.$el).find(".selectpicker").selectpicker("refresh");
        },

        computed: {
            searchUnits: function () {
                return this.units.filter((unit) => {
                    return unit.name_i18n.en.toLowerCase().match(this.search.toLowerCase());
                });
            },
            maxAttributes: function () {
                return this.selected.level * 10;
            },
        },

        methods: {
            load: function () {
                this.loading = true;

                window.axios.get("/frontend/units").then(response => {
                    this.units = response.data;
                    this.select(this.id === undefined ? this.units[0] : this.units.find((unit) => unit.id === this.id));
                }).then(() => {
                    window.axios.get("/frontend/behaviours").then(response => {
                        this.behaviours = response.data;
                    });
                }).then(() => {
                    window.axios.get("/frontend/items").then(response => {
                        this.items = response.data;
                    });
                }).then(() => {
                    window.axios.get("/frontend/skills").then(response => {
                        this.skills = response.data;
                    });
                }).then(() => {
                    window.axios.get("/frontend/loot").then(response => {
                        this.loot = response.data;
                    });
                }).then(() => {
                    window.axios.get("/frontend/environments").then(response => {
                        this.environments = response.data;
                    });
                }).then(() => {
                    window.axios.get("/frontend/behaviour_trees?type=Tree").then(response => {
                        this.behaviourTrees = response.data;
                    });
                }).then(() => {
                    window.axios.get("/frontend/archetypes").then(response => {
                        this.archetypes = response.data;
                        this.loading = false;
                    });
                });
            },

            getBehavioursInfo: function(item) {
                let icons = [];

                for (let key in item.behaviours) {
                    if (this.window.library.icons[item.behaviours[key].icon] === undefined) {
                        continue;
                    }

                    icons.push(this.window.library.icons[item.behaviours[key].icon].url);
                }

                if (icons.length === 0) {
                    return '';
                }

                let table = "<table><tr>";

                for (let key in icons) {
                    table += '<td><img style="width: 24px;" src="' + icons[key] + '"/></td>';
                }

                table + "</tr></table>";

                return table;
            },

            getQuestMarker: function(item) {
                if (item.flags.some(flag => flag === "Boss")) {
                    return "Boss";
                }

                if (item.flags.some(flag => flag === "Quest")) {
                    return "Quest";
                }

                if (item.flags.some(flag => flag === "Summoned")) {
                    return "Summoned";
                }

                return "";
            },

            getFilters: function () {
                return [
                    {
                        title: "Environment",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.environments,
                            valuecallback: (environment) => environment.id,
                            labelcallback: (environment) => environment.name_i18n.en
                        },
                        filter: (element, value) => {
                            return !value || element.environment_id === value
                        },
                        value: null
                    },
                    {
                        title: "Armor Type",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.library.armorSoundTypes
                        },
                        filter: (element, value) => {
                            return !value || value.length === 0 || element.armor_sound_type == value;
                        },
                        value: null
                    },
                    {
                        title: "Flags",
                        component: "flags",
                        props: {
                            flags: this.library.unitFlags
                        },
                        filter: (element, value) => {
                            return !value || value.length === 0 || element.flags.some(flag => value.indexOf(flag) >= 0);
                        },
                        value: null
                    },
                    {
                        title: "Don't have flags",
                        component: "flags",
                        props: {
                            flags: this.library.unitFlags
                        },
                        filter: (element, value) => {
                            return !value || value.length === 0 || !element.flags.some(flag => value.indexOf(flag) >= 0);
                        },
                        value: null
                    },
                ];
            },

            addBehaviour: function (unit) {
                unit.behaviours.push({
                    id: this.behaviours[0] ? this.behaviours[0].id : undefined
                });
            },

            removeBehaviour: function (unit, behaviour) {
                let index = unit.behaviours.indexOf(behaviour);
                unit.behaviours.splice(index, 1);
            },

            addItem: function (unit) {
                unit.items.push({
                    id: this.items[0] ? this.items[0].id : undefined
                });
            },

            removeItem: function (unit, item) {
                let index = unit.items.indexOf(item);
                unit.items.splice(index, 1);
            },

            addSkill: function (unit) {
                unit.skills.push({
                    id: this.skills[0] ? this.skills[0].id : undefined
                });
            },

            removeSkill: function (unit, skill) {
                let index = unit.skills.indexOf(skill);
                unit.skills.splice(index, 1);
            },

            skillMoveUp: function (unit, skill) {
                let index = unit.skills.indexOf(skill);

                if (index === -1 || index === 0) {
                    return;
                }

                unit.skills[index] = unit.skills[index - 1];
                unit.skills[index - 1] = skill;

                this.$forceUpdate();
            },

            skillMoveDown: function (unit, skill) {
                let index = unit.skills.indexOf(skill);

                if (index === -1 || index === unit.skills.length) {
                    return;
                }

                unit.skills[index] = unit.skills[index + 1];
                unit.skills[index + 1] = skill;

                this.$forceUpdate();
            },

            addLoot: function (unit) {
                unit.loot.push({
                    id: this.loot[0] ? this.loot[0].id : undefined
                });
            },

            removeLoot: function (unit, loot) {
                let index = unit.loot.indexOf(loot);
                unit.loot.splice(index, 1);
            },

            select: function (unit) {
                this.selected = unit;
            },

            create: function () {
                let fresh = {
                    fresh: true,
                    actor: "",
                    attributes: [],
                    properties: [],
                    is_playable: false,
                    is_blocks_movement: true,
                    skills: [],
                    name_i18n: {en: ""},
                    loot: [],
                    flags: [],
                    items: []
                };

                this.units.push(fresh);

                this.select(this.units[this.units.length - 1]);
            },

            duplicate: function (unit) {
                let copy = Object.assign({}, unit);
                copy.fresh = true;
                copy.id = "";

                this.units.push(copy);
                this.select(copy);
            },

            reset: function (unit) {
                if (unit.fresh) {
                    return;
                }

                let index = this.units.indexOf(unit);

                window.axios.get("/frontend/units/" + unit.id).then(response => {
                    this.units[index] = response.data;
                    this.select(this.units[index]);
                });
            },

            destroy: function (unit) {
                if (!confirm("Confirm?")) {
                    return;
                }

                let index = this.units.indexOf(unit);

                if (unit.fresh) {
                    this.units.splice(index, 1);
                    this.select(this.units[index]);
                    return;
                }

                window.axios.delete("/frontend/units/" + unit.id).then(response => {
                    this.units.splice(index, 1);
                    this.select(this.units[index]);
                });
            },

            save: function (unit) {
                let index = this.units.indexOf(unit);

                if (unit.fresh) {
                    window.axios.post("/frontend/units", unit).then(response => {
                        Object.assign(this.units[index], response.data);
                        this.units[index].fresh = false;
                        this.select(this.units[index]);
                    });

                    return;
                }

                window.axios.put("/frontend/units/" + unit.id, unit).then(response => {
                    Object.assign(this.units[index], response.data);
                    this.units[index].fresh = false;
                    this.select(this.units[index]);
                });
            },

            saveAll: function () {
                for (let key in this.units) {
                    let unit = this.units[key];

                    if (unit.fresh) {
                        window.axios.post("/frontend/units", unit);
                        continue;
                    }

                    window.axios.put("/frontend/units/" + unit.id, unit);
                }

                this.load();
            }
        }
    };
</script>
