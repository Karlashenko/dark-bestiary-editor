<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="loot" :fields="['id', 'name']" v-model="selected"></list>
                </div>

                <div class="col-md-9">
                    <div class="panel panel-default" v-if="selected">
                        <div class="panel-body">
                            <table class="form-table">
                                <tr>
                                    <td style="width: 15%;">Id</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.id" disabled />
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Name</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.name">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Count</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.count">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3"><hr>
                                        <table class="table-inner">
                                            <thead>
                                            <tr>
                                                <th style="width: 10%">Type</th>
                                                <th style="width: 20%">Value</th>
                                                <th style="width: 10%">Rarity</th>
                                                <th style="width: 10%">Probability</th>
                                                <th style="width: 10%">Stack Min</th>
                                                <th style="width: 10%">Stack Max</th>
                                                <th style="width: 10%">Enabled</th>
                                                <th style="width: 10%">Unique</th>
                                                <th style="width: 10%">Guaranteed</th>
                                                <th style="width: 10%">Ignore Level</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="item in selected.items">
                                                <td>
                                                    <select class="form-control" v-model="item.type">
                                                        <option v-for="lootItemType in window.library.lootItemTypes" :value="lootItemType">{{ lootItemType }}</option>
                                                    </select>
                                                </td>

                                                <td>
                                                    <div :class="{hidden: item.type !== 'Item'}">
                                                        <item-field v-model="item.item_id" :items="items"></item-field>
                                                    </div>

                                                    <div :class="{hidden: item.type !== 'Table'}">
                                                        <selectpicker v-model="item.table_id"
                                                                      :options="loot"
                                                                      :valuecallback="(element) => element.id"
                                                                      :labelcallback="(element) => element.name">
                                                        </selectpicker>
                                                    </div>

                                                    <div :class="{hidden: item.type !== 'Random'}">
                                                        <selectpicker v-model="item.category_id"
                                                                      :options="itemCategories"
                                                                      :valuecallback="(itemCategory) => itemCategory.id"
                                                                      :labelcallback="(itemCategory) => itemCategory.type">
                                                        </selectpicker>
                                                    </div>
                                                </td>

                                                <td>
                                                    <selectpicker v-model="item.rarity_id"
                                                                  :nullable="true"
                                                                  :options="itemRarities"
                                                                  :valuecallback="(itemRarity) => itemRarity.id"
                                                                  :labelcallback="(itemRarity) => itemRarity.type">
                                                    </selectpicker>
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control" v-model="item.probability">
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control" v-model="item.stack_count_min">
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control" v-model="item.stack_count_max">
                                                </td>

                                                <td style="text-align: center;">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" v-model="item.is_enabled">
                                                    </div>
                                                </td>

                                                <td style="text-align: center;">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" v-model="item.is_unique">
                                                    </div>
                                                </td>

                                                <td style="text-align: center;">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" v-model="item.is_guaranteed">
                                                    </div>
                                                </td>

                                                <td style="text-align: center;">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" v-model="item.is_ignore_level">
                                                    </div>
                                                </td>

                                                <td>
                                                    <button class="btn btn-default" @click="removeItem(selected, item)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="9">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addItem(selected)">
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
    import { Events } from './../events';

    export default {
        data() {
            return {
                loading: false,
                loot: [],
                items: [],
                itemRarities: [],
                itemCategories: [],
                window: window,
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
            calculateChance: function (item) {
                let sum = 0;

                for (let key in this.selected.items) {
                    if (this.selected.items[key].is_guaranteed)
                        continue;

                    sum += parseFloat(this.selected.items[key].probability);
                }

                return ((parseFloat(item.probability) / sum) * 100).toFixed(2) + '%';
            },

            load: function () {
                window.axios.get('/frontend/loot').then(response => {
                    this.loot = response.data;
                    this.select(this.id === undefined ? this.loot[0] : this.loot.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/items').then(response => {
                    this.items = response.data;
                });

                window.axios.get('/frontend/item_rarities').then(response => {
                    this.itemRarities = response.data;
                });

                window.axios.get('/frontend/item_categories').then(response => {
                    this.itemCategories = response.data;
                });
            },

            addItem: function (loot) {
                loot.items.push({
                    type: 'Item',
                    item_id: this.items[0].id,
                    is_enabled: true,
                    is_unique: true
                });
            },

            removeItem: function (loot, item) {
                let index = loot.items.indexOf(item);
                loot.items.splice(index, 1);
            },

            select: function (loot) {
                this.selected = loot;
            },

            create: function (loot) {
                this.loot.push({
                    fresh: true,
                    name: "New Element " + this.loot.length,
                    items: []
                });

                this.select(this.loot[this.loot.length - 1]);
            },

            duplicate: function (loot) {
                let copy = Object.assign({}, loot);
                copy.fresh = true;
                copy.id = '';
                copy.name = copy.name + " copy";

                this.loot.push(copy);
                this.select(copy);
            },

            reset: function (loot) {
                if (loot.fresh) {
                    return;
                }

                let index = this.loot.indexOf(loot);

                window.axios.get('/frontend/loot/' + loot.id).then(response => {
                    this.loot[index] = response.data;
                    this.select(this.loot[index]);
                });
            },

            destroy: function (loot) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.loot.indexOf(loot);

                if (loot.fresh) {
                    this.loot.splice(index, 1);
                    this.select(this.loot[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/loot/' + loot.id).then(response => {
                    this.loot.splice(index, 1);
                    this.select(this.loot[index <= 0 ? 0 : index - 1]);
                });
            },

            save: function (loot) {
                let index = this.loot.indexOf(loot);

                if (loot.fresh) {
                    window.axios.post('/frontend/loot', loot).then(response => {
                        this.loot[index] = response.data;
                        this.select(this.loot[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/loot/' + loot.id, loot).then(response => {
                    this.loot[index] = response.data;
                    this.select(this.loot[index]);
                });
            },

            saveAll: function () {
                for (let key in this.loot) {
                    let loot = this.loot[key];

                    if (loot.fresh) {
                        window.axios.post('/frontend/loot', loot);
                        continue;
                    }

                    window.axios.put('/frontend/loot/' + loot.id, loot);
                }

                this.load();
            }
        }
    };
</script>
