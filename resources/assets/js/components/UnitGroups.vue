<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="unitGroups" :fields="['id', 'label']" v-model="selected"></list>
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

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Units</td>
                                    <td>
                                        <table style="width: 100%">
                                            <tr v-for="unit in selected.units">
                                                <td>
                                                    <div class="form-group">
                                                        <selectpicker v-model="unit.id"
                                                                      :nullable="true"
                                                                      :options="units"
                                                                      :valuecallback="(element) => element.id"
                                                                      :labelcallback="(element) => element.name_i18n.en">
                                                        </selectpicker>
                                                    </div>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeUnit(selected, category)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="2">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addUnit(selected)">
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
    </div>
</template>

<script>
    import {Events} from './../events';

    export default {
        data() {
            return {
                unitGroups: [],
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
                window.axios.get('/frontend/unit_groups').then(response => {
                    this.unitGroups = response.data;
                    this.select(this.id === undefined ? this.unitGroups[0] : this.unitGroups.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/units').then(response => {
                    this.units = response.data;
                });
            },

            addUnit: function (unitGroup) {
                unitGroup.units.push({
                    id: this.units[0] ? this.units[0].id : undefined
                });
            },

            removeUnit: function (unitGroup, unit) {
                let index = unitGroup.units.indexOf(unit);
                unitGroup.units.splice(index, 1);
            },

            create: function () {
                this.unitGroups.push({
                    fresh: true,
                    units: [],
                    label: "Just Another Unit Group"
                });

                this.select(this.unitGroups[this.unitGroups.length - 1]);
            },

            select: function (unitGroup) {
                this.selected = unitGroup;
            },

            duplicate: function (unitGroup) {
                let copy = Object.assign({}, unitGroup);
                copy.fresh = true;
                this.unitGroups.push(copy);
                this.select(copy);
            },

            reset: function (unitGroup) {
                if (unitGroup.fresh) {
                    return;
                }

                let index = this.unitGroups.indexOf(unitGroup);

                window.axios.get('/frontend/unit_groups/' + unitGroup.id).then(response => {
                    this.unitGroups[index] = response.data;
                    this.select(this.unitGroups[index]);
                });
            },

            save: function (unitGroup) {
                let index = this.unitGroups.indexOf(unitGroup);

                if (unitGroup.fresh) {
                    window.axios.post('/frontend/unit_groups', unitGroup).then(response => {
                        this.unitGroups[index] = response.data;
                        this.select(this.unitGroups[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/unit_groups/' + unitGroup.id, unitGroup).then(response => {
                    this.unitGroups[index] = response.data;
                    this.select(this.unitGroups[index]);
                });
            },

            destroy: function (unitGroup) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.unitGroups.indexOf(unitGroup);

                if (unitGroup.fresh) {
                    this.unitGroups.splice(index, 1);
                    this.select(this.unitGroups[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/unit_groups/' + unitGroup.id).then(() => {
                    this.unitGroups.splice(index, 1);
                    this.select(this.unitGroups[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.unitGroups) {
                    let unitGroup = this.unitGroups[key];

                    if (unitGroup.fresh) {
                        window.axios.post('/frontend/unit_groups', unitGroup).then(response => {
                            this.unitGroups[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/unit_groups/' + unitGroup.id, unitGroup).then(response => {
                        this.unitGroups[key] = response.data;
                    });
                }
            }
        }
    };
</script>
