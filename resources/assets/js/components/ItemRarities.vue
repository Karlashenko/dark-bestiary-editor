<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="rarities" :fields="['id', 'name_i18n.en', 'type']" v-model="selected"></list>
                </div>

                <div class="col-md-9">
                    <div class="panel panel-default" v-if="selected">
                        <div class="panel-body">
                            <table class="form-table">
                                <tr>
                                    <td>Id</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.id" disabled>
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

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Name I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.name_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <div class="form-group">
                                            <select class="form-control selectpicker" data-live-search="true" v-model="selected.type">
                                                <option :value="null">NULL</option>
                                                <option v-for="itemRarityType in window.library.itemRarityTypes" :value="itemRarityType" :selected="itemRarityType === selected.type">
                                                    {{ itemRarityType }}
                                                </option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Color Code</td>
                                    <td>
                                        <div class="form-group">
                                            <input style="display: inline; width: 30%;" type="text" class="form-control" v-model="selected.color_code">
                                            <input style="display: inline; width: 48px;" type="color" class="form-control" v-model="selected.color_code">
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
                rarities: [],
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

        updated () {
            $(this.$el).find('.selectpicker').selectpicker('refresh');
        },

        methods: {
            load: function () {
                window.axios.get('/frontend/item_rarities').then(response => {
                    this.rarities = response.data;
                    this.select(this.id === undefined ? this.rarities[0] : this.rarities.find((element) => element.id === this.id));
                });
            },

            create: function () {
                this.rarities.push({
                    fresh: true,
                    name_i18n: {en: "rarity_"}
                });

                this.select(this.rarities[this.rarities.length - 1]);
            },

            select: function (rarity) {
                this.selected = rarity;
            },

            duplicate: function (rarity) {
                let copy = Object.assign({}, rarity);
                copy.fresh = true;
                this.rarities.push(copy);
                this.select(copy);
            },

            reset: function (rarity) {
                if (rarity.fresh) {
                    return;
                }

                let index = this.rarities.indexOf(rarity);

                window.axios.get('/frontend/item_rarities/' + rarity.id).then(response => {
                    this.rarities[index] = response.data;
                    this.select(this.rarities[index]);
                });
            },

            save: function (rarity) {
                let index = this.rarities.indexOf(rarity);

                if (rarity.fresh) {
                    window.axios.post('/frontend/item_rarities', rarity).then(response => {
                        this.rarities[index] = response.data;
                        this.select(this.rarities[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/item_rarities/' + rarity.id, rarity).then(response => {
                    this.rarities[index] = response.data;
                    this.select(this.rarities[index]);
                });
            },

            destroy: function (rarity) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.rarities.indexOf(rarity);

                if (rarity.fresh) {
                    this.rarities.splice(index, 1);
                    this.select(this.rarities[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/item_rarities/' + rarity.id).then(() => {
                    this.rarities.splice(index, 1);
                    this.select(this.rarities[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.rarities) {
                    let rarity = this.rarities[key];

                    if (rarity.fresh) {
                        window.axios.post('/frontend/item_rarities', rarity).then(response => {
                            this.rarities[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/item_rarities/' + rarity.id, rarity).then(response => {
                        this.rarities[key] = response.data;
                    });
                }
            }
        }
    };
</script>
