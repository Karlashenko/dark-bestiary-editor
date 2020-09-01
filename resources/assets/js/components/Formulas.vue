<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="formulas" :fields="['id', 'name']" v-model="selected"></list>
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
                                    <td>Name</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.name">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Formula</td>
                                    <td>
                                        <selectpicker v-model="selected.formula_id"
                                                      :nullable="true"
                                                      :options="formulas"
                                                      :valuecallback="(formula) => formula.id"
                                                      :labelcallback="(formula) => formula.name">
                                        </selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Text</td>
                                    <td>
                                        <div class="form-group">
                                            <textarea class="form-control" v-model="selected.text" style="font-family:monospace;"></textarea>
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
                formulas: [],
                environments: [],
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
            create: function () {
                this.formulas.push({
                    name: "Just Another Formula",
                    fresh: true
                });

                this.select(this.formulas[this.formulas.length - 1]);
            },

            saveAll: function () {
                for (let key in this.formulas) {
                    let formula = this.formulas[key];

                    if (formula.fresh) {
                        window.axios.post('/frontend/formulas', formula).then(response => {
                            this.formulas[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put('/frontend/formulas/' + formula.id, formula).then(response => {
                        this.formulas[key] = response.data;
                    });
                }
            },

            load: function () {
                window.axios.get('/frontend/formulas').then(response => {
                    this.formulas = response.data;
                    this.select(this.id === undefined ? this.formulas[0] : this.formulas.find((element) => element.id === this.id));
                });

                window.axios.get('/frontend/environments').then(response => {
                    this.environments = response.data;
                });
            },

            select: function (formula) {
                this.selected = formula;
            },

            duplicate: function (formula) {
                let copy = Object.assign({}, formula);
                copy.fresh = true;
                this.formulas.push(copy);
                this.select(copy);
            },

            reset: function (formula) {
                if (formula.fresh) {
                    return;
                }

                let index = this.formulas.indexOf(formula);

                window.axios.get('/frontend/formulas/' + formula.id).then(response => {
                    this.formulas[index] = response.data;
                    this.select(this.formulas[index]);
                });
            },

            save: function (formula) {
                let index = this.formulas.indexOf(formula);

                if (formula.fresh) {
                    window.axios.post('/frontend/formulas', formula).then(response => {
                        this.formulas[index] = response.data;
                        this.select(this.formulas[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/formulas/' + formula.id, formula).then(response => {
                    this.formulas[index] = response.data;
                    this.select(this.formulas[index]);
                });
            },

            destroy: function (formula) {
                if (!confirm('Confirm?')) {
                    return;
                }

                let index = this.formulas.indexOf(formula);

                if (formula.fresh) {
                    this.formulas.splice(index, 1);
                    this.select(this.formulas[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete('/frontend/formulas/' + formula.id).then(response => {
                    this.formulas.splice(index, 1);
                    this.select(this.formulas[index <= 0 ? 0 : index - 1]);
                });
            }
        }
    };
</script>
