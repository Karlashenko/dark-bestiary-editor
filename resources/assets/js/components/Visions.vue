<template>
    <div>
        <div>
            <div v-if="loading" class="loading"></div>

            <div class="row" v-if="!loading">
                <div class="col-md-4">
                    <list :elements="visions" :fields="['id', 'probability', 'name_i18n.en', 'type', 'label', 'rarity.type']" :icon="'icon'" v-model="selected"></list>
                </div>

                <div class="col-md-8">
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
                                    <td>Type</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.type">
                                        </div>
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
                                    <td>Icon</td>
                                    <td>
                                        <icon-field v-model="selected.icon"></icon-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Rarity</td>
                                    <td>
                                        <div class="form-group">
                                            <selectpicker v-model="selected.rarity_id"
                                                          :options="rarities"
                                                          :valuecallback="(rarity) => rarity.id"
                                                          :labelcallback="(rarity) => rarity.type">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Sound</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.sound">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Prefab</td>
                                    <td>
                                        <prefab-field v-model="selected.prefab"></prefab-field>
                                    </td>
                                </tr>

                                <tr><td colspan="2"><hr></td></tr>

                                <tr>
                                    <td>Act Min</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.act_min">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Act Max</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.act_max">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Is Final</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.is_final">
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="2"><hr></td></tr>

                                <tr>
                                    <td>Probability</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.probability">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Is Enabled</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.is_enabled">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Is Unique</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.is_unique">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Is Guaranteed</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.is_guaranteed">
                                        </div>
                                    </td>
                                </tr>

                                <tr><td colspan="2"><hr></td></tr>

                                <tr>
                                    <td style="white-space: nowrap;">Visions</td>
                                    <td style="width: 100%">
                                        <table style="width: 100%; table-layout: auto;">
                                            <tr v-for="vision in selected.visions">
                                                <td style="width: 100%;">
                                                    <selectpicker v-model="vision.id"
                                                                  :options="visions"
                                                                  :valuecallback="(element) => element.id"
                                                                  :labelcallback="(element) => element.name_i18n.en + ' (' + element.label + ' ' + element.rarity.type + ')'">
                                                    </selectpicker>
                                                </td>

                                                <td style="padding-left: 4px; vertical-align: top; white-space: nowrap;">
                                                    <button class="btn btn-default" @click="visionMoveUp(selected, vision)">
                                                        <span class="glyphicon glyphicon-arrow-up"></span>
                                                    </button>

                                                    <button class="btn btn-default" @click="visionMoveDown(selected, vision)">
                                                        <span class="glyphicon glyphicon-arrow-down"></span>
                                                    </button>

                                                    <button class="btn btn-default" @click="removeVision(selected, vision)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addVision(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Loot</td>
                                    <td>
                                        <selectpicker v-model="selected.loot_id"
                                                      :nullable="true"
                                                      :options="loot"
                                                      :valuecallback="(loot) => loot.id"
                                                      :labelcallback="(loot) => loot.name">
                                        </selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Scenario</td>
                                    <td>
                                        <selectpicker v-model="selected.scenario_id"
                                                      :nullable="true"
                                                      :options="scenarios"
                                                      :valuecallback="(scenario) => scenario.id"
                                                      :labelcallback="(scenario) => window.getPropertyByPath(scenario, 'name_i18n.en|label')">
                                        </selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Effect</td>
                                    <td>
                                        <effect-field v-model="selected.effect_id" :effects="effects"></effect-field>
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
    import {Events} from "./../events";

    export default {
        props: ["id"],

        data() {
            return {
                loading: true,
                visions: [],
                loot: [],
                effects: [],
                scenarios: [],
                rarities: [],
                window: window,
                library: window.library,
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

        methods: {
            load: function () {
                this.loading = true;

                window.axios.get("/frontend/visions").then(response => {
                    this.visions = response.data;
                    this.loading = false;
                    this.select(this.id === undefined ? this.visions[0] : this.visions.find((vision) => vision.id === this.id));
                });

                window.axios.get('/frontend/item_rarities').then(response => {
                    this.rarities = response.data;
                });

                window.axios.get("/frontend/scenarios").then(response => {
                    this.scenarios = response.data;
                });

                window.axios.get("/frontend/effects").then(response => {
                    this.effects = response.data;
                });

                window.axios.get("/frontend/loot").then(response => {
                    this.loot = response.data;
                });
            },

            addVision: function (item) {
                if (item.visions === undefined) {
                    item.visions = [];
                }

                item.visions.push({
                    id: this.visions[0] ? this.visions[0].id : undefined
                });
            },

            removeVision: function (item, vision) {
                let index = item.visions.indexOf(vision);
                item.visions.splice(index, 1);
            },

            visionMoveUp: function (item, vision) {
                let index = item.visions.indexOf(vision);

                if (index === -1 || index === 0) {
                    return;
                }

                item.visions[index] = item.visions[index - 1];
                item.visions[index - 1] = vision;

                this.$forceUpdate();
            },

            visionMoveDown: function (item, vision) {
                let index = item.visions.indexOf(vision);

                if (index === -1 || index === item.visions.length) {
                    return;
                }

                item.visions[index] = item.visions[index + 1];
                item.visions[index + 1] = vision;

                this.$forceUpdate();
            },

            select: function (vision) {
                this.selected = vision;
            },

            create: function () {
                let fresh = {
                    fresh: true,
                    visions: [],
                    name_i18n: {
                        en: ""
                    }
                };

                this.visions.push(fresh);

                this.select(this.visions[this.visions.length - 1]);
            },

            duplicate: function (vision) {
                let copy = Object.assign({}, vision);
                copy.fresh = true;
                copy.id = "";

                this.visions.push(copy);
                this.select(copy);
            },

            reset: function (vision) {
                if (vision.fresh) {
                    return;
                }

                let index = this.visions.indexOf(vision);

                window.axios.get("/frontend/visions/" + vision.id).then(response => {
                    this.visions[index] = response.data;
                    this.select(this.visions[index]);
                });
            },

            destroy: function (vision) {
                if (!confirm("Confirm?")) {
                    return;
                }

                let index = this.visions.indexOf(vision);

                if (vision.fresh) {
                    this.visions.splice(index, 1);
                    this.select(this.visions[index]);
                    return;
                }

                window.axios.delete("/frontend/visions/" + vision.id).then(response => {
                    this.visions.splice(index, 1);
                    this.select(this.visions[index]);
                });
            },

            save: function (vision) {
                let index = this.visions.indexOf(vision);

                if (vision.fresh) {
                    window.axios.post("/frontend/visions", vision).then(response => {
                        Object.assign(this.visions[index], response.data);
                        this.visions[index].fresh = false;
                        this.select(this.visions[index]);
                    });

                    return;
                }

                window.axios.put("/frontend/visions/" + vision.id, vision).then(response => {
                    Object.assign(this.visions[index], response.data);
                    this.visions[index].fresh = false;
                    this.select(this.visions[index]);
                });
            },

            saveAll: function () {
                for (let key in this.visions) {
                    let vision = this.visions[key];

                    if (vision.fresh) {
                        window.axios.post("/frontend/visions", vision);
                        continue;
                    }

                    window.axios.put("/frontend/visions/" + vision.id, vision);
                }

                this.load();
            }
        }
    };
</script>
