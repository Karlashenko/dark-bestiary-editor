<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="validators" :fields="['id', 'name']" :icon="icon" v-model="selected"></list>
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
                                    <td>Name</td>
                                    <td>
                                        <input type="text" class="form-control" v-model="selected.name">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <selectpicker v-model="selected.type" :options="window.library.validatorTypes"></selectpicker>
                                    </td>
                                </tr>

                                <tr><td colspan="2"><hr></td></tr>

                                <tr>
                                    <td>Value</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.value">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Fraction</td>
                                    <td>
                                        <input type="number" class="form-control" v-model="selected.fraction">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Comparator</td>
                                    <td>
                                        <selectpicker v-model="selected.comparator" :options="window.library.comparatorTypes"></selectpicker>
                                    </td>
                                </tr>

                                <tr><td colspan="2"><hr></td></tr>

                                <tr>
                                    <td>Behaviour</td>
                                    <td>
                                        <behaviour-field v-model="selected.behaviour_id" :behaviours="behaviours"></behaviour-field>
                                    </td>
                                </tr>

                                <tr><td colspan="2"><hr></td></tr>

                                <tr>
                                    <td>Range Min</td>
                                    <td>
                                        <input type="text" class="form-control" v-model="selected.range_min">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Range Max</td>
                                    <td>
                                        <input type="text" class="form-control" v-model="selected.range_max">
                                    </td>
                                </tr>

                                <tr><td colspan="2"><hr></td></tr>

                                <tr>
                                    <td>Unit</td>
                                    <td>
                                        <unit-field v-model="selected.unit_id" :units="units"></unit-field>
                                    </td>
                                </tr>

                                <tr><td colspan="2"><hr></td></tr>

                                <tr>
                                    <td>Scenario</td>
                                    <td>
                                        <selectpicker v-model="selected.scenario_id"
                                                      :options="scenarios"
                                                      :valuecallback="(scenario) => scenario.id"
                                                      :labelcallback="(scenario) => scenario.name_i18n.en">
                                        </selectpicker>
                                    </td>
                                </tr>

                                <tr><td colspan="2"><hr></td></tr>

                                <tr>
                                    <td>Validators</td>
                                    <td>
                                        <table>
                                            <tbody>
                                            <tr v-for="validator in selected.validators">
                                                <td style="width: 30%;">
                                                    <selectpicker v-model="validator.id"
                                                                  :options="validators"
                                                                  :valuecallback="(element) => element.id"
                                                                  :labelcallback="(element) => element.name">
                                                    </selectpicker>
                                                </td>

                                                <td style="width: 10%;">
                                                    <button class="btn btn-default" @click="removeValidator(selected, validator)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="2">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addValidator(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                                <tr><td colspan="2"><hr></td></tr>

                                <tr>
                                    <td>Status Flags</td>
                                    <td>
                                        <flags v-model="selected.status_flags" :flags="window.library.behaviourStatusFlags"></flags>
                                    </td>
                                </tr>

                                <tr><td colspan="2"><hr></td></tr>

                                <tr>
                                    <td>Unit Flags</td>
                                    <td>
                                        <flags v-model="selected.unit_flags" :flags="window.library.unitFlags"></flags>
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
        data() {
            return {
                validators: [],
                attributes: [],
                properties: [],
                units: [],
                scenarios: [],
                behaviours: [],
                window: window,
                selected: undefined
            };
        },

        mounted() {
            Events.$off();
            Events.$on("toolbar.load", this.load);
            Events.$on("toolbar.create", this.create);
            Events.$on("toolbar.save", this.saveAll);
            Events.$on("toolbar.download", this.download);

            this.load();
        },

        methods: {
            load: function () {
                window.axios.get("/frontend/validators").then(response => {
                    this.validators = response.data;
                    this.select(this.id === undefined ? this.validators[0] : this.validators.find((element) => element.id === this.id));
                });

                window.axios.get("/frontend/attributes").then(response => {
                    this.attributes = response.data;
                });

                window.axios.get("/frontend/properties").then(response => {
                    this.properties = response.data;
                });

                window.axios.get("/frontend/scenarios").then(response => {
                    this.scenarios = response.data;
                });

                window.axios.get("/frontend/units").then(response => {
                    this.units = response.data;
                });

                window.axios.get("/frontend/behaviours").then(response => {
                    this.behaviours = response.data;
                });
            },

            create: function () {
                this.validators.push({
                    fresh: true,
                    name: "Just Another Validator " + this.validators.length + 1,
                    unit_flags: [],
                    status_flags: [],
                    validators: [],
                });

                this.select(this.validators[this.validators.length - 1]);
            },

            select: function (validator) {
                this.selected = validator;
            },

            duplicate: function (validator) {
                let copy = Object.assign({}, validator);
                copy.fresh = true;
                this.validators.push(copy);
                this.select(copy);
            },

            addValidator: function (item) {
                item.validators.push({
                    validator_id: this.validators[0] && this.validators.length > 1 ? this.validators[0].id : null
                });
            },

            removeValidator: function (item, validator) {
                let index = item.validators.indexOf(validator);
                item.validators.splice(index, 1);
            },

            reset: function (validator) {
                if (validator.fresh) {
                    return;
                }

                let index = this.validators.indexOf(validator);

                window.axios.get("/frontend/validators/" + validator.id).then(response => {
                    this.validators[index] = response.data;
                    this.select(this.validators[index]);
                });
            },

            save: function (validator) {
                let index = this.validators.indexOf(validator);

                if (validator.fresh) {
                    window.axios.post("/frontend/validators", validator).then(response => {
                        this.validators[index] = response.data;
                        this.select(this.validators[index]);
                    });

                    return;
                }

                window.axios.put("/frontend/validators/" + validator.id, validator).then(response => {
                    this.validators[index] = response.data;
                    this.select(this.validators[index]);
                });
            },

            destroy: function (validator) {
                if (!confirm("Confirm?")) {
                    return;
                }

                let index = this.validators.indexOf(validator);

                if (validator.fresh) {
                    this.validators.splice(index, 1);
                    this.select(this.validators[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete("/frontend/validators/" + validator.id).then(() => {
                    this.validators.splice(index, 1);
                    this.select(this.validators[index <= 0 ? 0 : index - 1]);
                });
            },

            saveAll: function () {
                for (let key in this.validators) {
                    let validator = this.validators[key];

                    if (validator.fresh) {
                        window.axios.post("/frontend/validators", validator).then(response => {
                            this.validators[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put("/frontend/validators/" + validator.id, validator).then(response => {
                        this.validators[key] = response.data;
                    });
                }
            }
        }
    };
</script>
