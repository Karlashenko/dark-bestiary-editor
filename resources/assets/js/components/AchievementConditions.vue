<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="conditions" :fields="['id', 'label', 'type']" v-model="selected"></list>
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
                                    <td>Label</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.label">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <selectpicker v-model="selected.type" :options="window.library.achievementConditionTypes"></selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Comparator</td>
                                    <td>
                                        <selectpicker v-model="selected.comparator" :options="window.library.comparatorTypes"></selectpicker>
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

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Unit</td>
                                    <td>
                                        <unit-field v-model="selected.unit_id" :units="units"></unit-field>
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
                conditions: [],
                behaviours: [],
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
                window.axios.get('/frontend/achievement_conditions').then(response => {
                    this.conditions = response.data;
                    this.select(this.id === undefined ? this.conditions[0] : this.conditions.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/units').then(response => {
                    this.units = response.data;
                });

                window.axios.get('/frontend/behaviours').then(response => {
                    this.behaviours = response.data;
                });
            },

            create: function () {
                this.conditions.push({
                    fresh: true,
                    label: 'Just Another Achievement Condition'
                });

                this.select(this.conditions[this.conditions.length - 1]);
            },

            select: function (condition) {
                this.selected = condition;
            },

            duplicate: function (condition) {
                let copy = Object.assign({}, condition);
                copy.fresh = true;
                this.conditions.push(copy);
                this.select(copy);
            },

            reset: function (condition) {
                if (condition.fresh) {
                    return;
                }

                let index = this.conditions.indexOf(condition);

                window.axios.get('/frontend/achievement_conditions/' + condition.id).then(response => {
                    this.conditions[index] = response.data;
                    this.select(this.conditions[index]);
                });
            },

            save: function (condition) {
                let index = this.conditions.indexOf(condition);

                if (condition.fresh) {
                    window.axios.post('/frontend/achievement_conditions', condition).then(response => {
                        this.conditions[index] = response.data;
                        this.select(this.conditions[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/achievement_conditions/' + condition.id, condition).then(response => {
                    this.conditions[index] = response.data;
                    this.select(this.conditions[index]);
                });
            },

            destroy: function (condition) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.conditions.indexOf(condition);

                if (condition.fresh) {
                    this.conditions.splice(index, 1);
                    this.select(this.conditions[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/achievement_conditions/' + condition.id).then(() => {
                    this.conditions.splice(index, 1);
                    this.select(this.conditions[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.conditions) {
                    let condition = this.conditions[key];

                    if (condition.fresh) {
                        window.axios.post('/frontend/achievement_conditions', condition).then(response => {
                            this.conditions[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/achievement_conditions/' + condition.id, condition).then(response => {
                        this.conditions[key] = response.data;
                    });
                }
            }
        }
    };
</script>
