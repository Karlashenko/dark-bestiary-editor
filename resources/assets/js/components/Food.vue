<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="foods" :fields="['id', 'index', 'name_i18n.en', 'type']" :icon="'icon'" v-model="selected"></list>
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

                                <tr><td colspan="3"><hr></td></tr>

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
                                        <selectpicker v-model="selected.type" :options="window.library.foodTypes"></selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Price</td>
                                    <td>
                                        <input type="text" class="form-control" v-model="selected.price">
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

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
                foods: [],
                behaviours: [],
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
            addPropertyModifier: function (item) {
                item.property_modifiers.push({
                    property_id: this.properties[0] ? this.properties[0].id : undefined,
                    type: window.library.modifierTypes[0],
                    value: 0
                });
            },

            removePropertyModifier: function (item, modifier) {
                let index = item.property_modifiers.indexOf(modifier);
                item.property_modifiers.splice(index, 1);
            },

            load: function () {
                window.axios.get('/frontend/food').then(response => {
                    this.foods = response.data;
                    this.select(this.id === undefined ? this.foods[0] : this.foods.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/behaviours').then(response => {
                    this.behaviours = response.data;
                });
            },

            create: function () {
                this.foods.push({
                    fresh: true,
                    name_i18n: {en: "attribute_"}
                });

                this.select(this.foods[this.foods.length - 1]);
            },

            select: function (food) {
                this.selected = food;
            },

            duplicate: function (food) {
                let copy = Object.assign({}, food);
                copy.fresh = true;
                this.foods.push(copy);
                this.select(copy);
            },

            reset: function (food) {
                if (food.fresh) {
                    return;
                }

                let index = this.foods.indexOf(food);

                window.axios.get('/frontend/food/' + food.id).then(response => {
                    this.foods[index] = response.data;
                    this.select(this.foods[index]);
                });
            },

            save: function (food) {
                let index = this.foods.indexOf(food);

                if (food.fresh) {
                    window.axios.post('/frontend/food', food).then(response => {
                        this.foods[index] = response.data;
                        this.select(this.foods[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/food/' + food.id, food).then(response => {
                    this.foods[index] = response.data;
                    this.select(this.foods[index]);
                });
            },

            destroy: function (food) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.foods.indexOf(food);

                if (food.fresh) {
                    this.foods.splice(index, 1);
                    this.select(this.foods[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/food/' + food.id).then(() => {
                    this.foods.splice(index, 1);
                    this.select(this.foods[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.foods) {
                    let food = this.foods[key];

                    if (food.fresh) {
                        window.axios.post('/frontend/food', food).then(response => {
                            this.foods[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/food/' + food.id, food).then(response => {
                        this.foods[key] = response.data;
                    });
                }
            }
        }
    };
</script>
