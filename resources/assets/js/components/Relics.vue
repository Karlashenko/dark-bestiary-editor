<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="relics" :fields="['id', 'name_i18n.en']" :icon="'icon'" v-model="selected"></list>
                </div>

                <div class="col-md-9">
                    <div class="panel panel-default" v-if="selected">
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
                                    <td>Lore I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.lore_i18n_id"></i18n-field>
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
                                                          :options="itemRarities"
                                                          :valuecallback="(itemRarity) => itemRarity.id"
                                                          :labelcallback="(itemRarity) => itemRarity.type">
                                            </selectpicker>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Behaviour</td>
                                    <td>
                                        <behaviour-field v-model="selected.behaviour_id" :behaviours="behaviours"></behaviour-field>
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
                relics: [],
                behaviours: [],
                itemRarities: [],
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
                window.axios.get("/frontend/relics").then(response => {
                    this.relics = response.data;
                    this.select(this.id === undefined ? this.relics[0] : this.relics.find((element) => element.id === this.id));
                });

                window.axios.get("/frontend/behaviours").then(response => {
                    this.behaviours = response.data;
                });

                window.axios.get("/frontend/item_rarities").then(response => {
                    this.itemRarities = response.data;
                });
            },

            create: function () {
                this.relics.push({
                    fresh: true,
                    name_i18n: {en: "relic_"}
                });

                this.select(this.relics[this.relics.length - 1]);
            },

            select: function (relic) {
                this.selected = relic;
            },

            duplicate: function (relic) {
                let copy = Object.assign({}, relic);
                copy.fresh = true;
                this.relics.push(copy);
                this.select(copy);
            },

            reset: function (relic) {
                if (relic.fresh) {
                    return;
                }

                let index = this.relics.indexOf(relic);

                window.axios.get('/frontend/relics/' + relic.id).then(response => {
                    this.relics[index] = response.data;
                    this.select(this.relics[index]);
                });
            },

            save: function (relic) {
                let index = this.relics.indexOf(relic);

                if (relic.fresh) {
                    window.axios.post('/frontend/relics', relic).then(response => {
                        this.relics[index] = response.data;
                        this.select(this.relics[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/relics/' + relic.id, relic).then(response => {
                    this.relics[index] = response.data;
                    this.select(this.relics[index]);
                });
            },

            destroy: function (relic) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.relics.indexOf(relic);

                if (relic.fresh) {
                    this.relics.splice(index, 1);
                    this.select(this.relics[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/relics/' + relic.id).then(() => {
                    this.relics.splice(index, 1);
                    this.select(this.relics[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.relics) {
                    let relic = this.relics[key];

                    if (relic.fresh) {
                        window.axios.post('/frontend/relics', relic).then(response => {
                            this.relics[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/relics/' + relic.id, relic).then(response => {
                        this.relics[key] = response.data;
                    });
                }
            }
        }
    };
</script>
