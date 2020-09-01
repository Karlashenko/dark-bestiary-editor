<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="dialogues" :fields="['id', 'label', 'narrator', 'is_parent']" v-model="selected" :filters="getFilters()"></list>
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
                                    <td>Is Parent</td>
                                    <td>
                                        <input type="checkbox" v-model="selected.is_parent">
                                    </td>
                                </tr>

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
                                        <input type="text" class="form-control" v-model="selected.label">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Title</td>
                                    <td>
                                        <i18n-field v-model="selected.title_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Text</td>
                                    <td>
                                        <i18n-field v-model="selected.text_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Narrator</td>
                                    <td>
                                        <selectpicker v-model="selected.narrator" :nullable="true" :options="window.library.narrators"></selectpicker>
                                    </td>
                                </tr>

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Validators</td>
                                    <td>
                                        <table class="table-inner">
                                            <tbody>
                                            <tr v-for="validator in selected.validators">
                                                <td style="width: 50%;">
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

                                <tr><td colspan="3"><hr></td></tr>

                                <tr>
                                    <td>Actions</td>
                                    <td>
                                        <table class="table-inner">
                                            <tr v-for="action in selected.actions">
                                                <td style="width: 100%;">
                                                    <dialogue-action :entity="action" :dialogues="dialogues"></dialogue-action>
                                                </td>

                                                <td style="padding-left: 4px; vertical-align: top; white-space: nowrap;">
                                                    <button class="btn btn-default" @click="actionMoveUp(selected, action)">
                                                        <span class="glyphicon glyphicon-arrow-up"></span>
                                                    </button>

                                                    <button class="btn btn-default" @click="actionMoveDown(selected, action)">
                                                        <span class="glyphicon glyphicon-arrow-down"></span>
                                                    </button>

                                                    <button class="btn btn-default" @click="removeAction(selected, action)">
                                                        <span class="glyphicon glyphicon-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td>
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addAction(selected)">
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
    import {Events} from "./../events";

    export default {
        data() {
            return {
                window: window,
                dialogues: [],
                validators: [],
                selected: undefined
            };
        },

        mounted() {
            Events.$off();
            Events.$on("toolbar.load", this.load);
            Events.$on("toolbar.create", this.create);

            this.load();
        },

        methods: {
            load: function () {
                window.axios.get("frontend/dialogues").then(response => {
                    this.dialogues = response.data;
                    this.select(this.id === undefined ? this.dialogues[0] : this.dialogues.find((element) => element.id === this.id));
                });

                window.axios.get("frontend/validators").then(response => {
                    this.validators = response.data;
                });
            },

            create: function () {
                this.dialogues.push({
                    fresh: true,
                    label: "Dialogue_" + this.dialogues.length + 1,
                    validators: [],
                    actions: [],
                });

                this.select(this.dialogues[this.dialogues.length - 1]);
            },

            select: function (dialogue) {
                this.selected = dialogue;
            },

            addAction: function (dialogue) {
                if (dialogue.actions === undefined) {
                    dialogue.actions = [];
                }

                dialogue.actions.push({
                    type: "Dialogue",
                });
            },

            removeAction: function (dialogue, action) {
                let index = dialogue.actions.indexOf(action);
                dialogue.actions.splice(index, 1);
            },

            actionMoveUp: function (dialogue, action) {
                let index = dialogue.actions.indexOf(action);

                if (index === -1 || index === 0) {
                    return;
                }

                dialogue.actions[index] = dialogue.actions[index - 1];
                dialogue.actions[index - 1] = action;

                this.$forceUpdate();
            },

            actionMoveDown: function (dialogue, action) {
                let index = dialogue.actions.indexOf(action);

                if (index === -1 || index === dialogue.actions.length) {
                    return;
                }

                dialogue.actions[index] = dialogue.actions[index + 1];
                dialogue.actions[index + 1] = action;

                this.$forceUpdate();
            },

            addValidator: function (dialogue) {
                if (dialogue.validators === undefined) {
                    Object.assign(dialogue, {validators: []});
                }

                dialogue.validators.push({
                    id: this.validators[0] ? this.validators[0].id : undefined
                });
            },

            removeValidator: function (dialogue, validator) {
                let index = dialogue.validators.indexOf(validator);
                dialogue.validators.splice(index, 1);
            },

            getFilters: function () {
                return [
                    {
                        title: "Narrator",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.window.library.narrators,
                        },
                        filter: (element, value) => {
                            return !value || element.narrator === value
                        },
                        value: null
                    },
                    {
                        title: "Is Parent",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: [true, false]
                        },
                        filter: (element, value) => {
                            return value == null || element.is_parent === value;
                        },
                        value: null
                    },
                ];
            },

            duplicate: function (dialogue) {
                let copy = Object.assign({}, dialogue);
                copy.fresh = true;
                copy.id = '';
                copy.name = copy.name + " copy";
                copy.actions = [];

                for (let key in dialogue.actions) {
                    copy.actions.push(Object.assign({}, dialogue.actions[key]));
                }

                this.dialogues.push(copy);
                this.select(copy);
            },

            reset: function (dialogue) {
                if (dialogue.fresh) {
                    return;
                }

                let index = this.dialogues.indexOf(dialogue);

                window.axios.get("frontend/dialogues/" + dialogue.id).then(response => {
                    this.dialogues[index] = response.data;
                    this.select(this.dialogues[index]);
                });
            },

            destroy: function (dialogue) {
                if (!confirm("Confirm?")) {
                    return;
                }

                let index = this.dialogues.indexOf(dialogue);

                if (dialogue.fresh) {
                    this.dialogues.splice(index, 1);
                    this.select(this.dialogues[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete("frontend/dialogues/" + dialogue.id).then(() => {
                    this.dialogues.splice(index, 1);
                    this.select(this.dialogues[index <= 0 ? 0 : index - 1]);
                });
            },

            save: function (dialogue) {
                let index = this.dialogues.indexOf(dialogue);

                if (dialogue.fresh) {
                    window.axios.post('/frontend/dialogues', dialogue).then(response => {
                        this.dialogues[index] = response.data;
                        this.select(this.dialogues[index]);
                    });

                    return;
                }

                window.axios.put('/frontend/dialogues/' + dialogue.id, dialogue).then(response => {
                    this.dialogues[index] = response.data;
                    this.select(this.dialogues[index]);
                });
            }
        }
    };
</script>
