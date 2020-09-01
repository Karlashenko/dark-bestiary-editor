<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="nodes" :fields="['id', 'name', 'type']" v-model="selected"></list>
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
                                        <input type="text" class="form-control" v-model="selected.name">
                                    </td>
                                </tr>

                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <select class="form-control selectpicker" data-live-search="true" v-model="selected.type">
                                            <optgroup v-for="(group, label) in window.library.nodeGroupsDictionary" :label="label">
                                                <option v-for="type in group" :value="type" :selected="type === selected.type">
                                                    {{ type }}
                                                </option>
                                            </optgroup>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Comment</td>
                                    <td>
                                        <input type="text" class="form-control" v-model="selected.comment">
                                    </td>
                                </tr>
                            </table>

                            <hr>

                            <behaviour-tree-node ref="root" v-model="selected"></behaviour-tree-node>
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
                nodes: [],
                window: window,
                selected: undefined
            };
        },

        mounted() {
            Events.$off();
            Events.$on("toolbar.load", this.load);
            Events.$on("toolbar.create", this.create);

            this.load();
        },

        updated() {
            this.window.$(this.$el.querySelector(".selectpicker")).selectpicker("refresh");

            if (this.$refs["root"]) {
                this.$refs["root"].rebuild(this.selected);
            }
        },

        methods: {
            load: function () {
                window.axios.get("frontend/behaviour_trees?type=Tree,Template").then(response => {
                    this.nodes = response.data;
                    this.select(this.id === undefined ? this.nodes[0] : this.nodes.find((element) => element.id === this.id));
                });
            },

            create: function () {
                this.nodes.push({
                    fresh: true,
                    name: "Tree_" + this.nodes.length + 1,
                    type: "Tree",
                    group: "Composite",
                    comment: "",
                    properties: []
                });

                this.select(this.nodes[this.nodes.length - 1]);
            },

            select: function (node) {
                this.selected = node;

                if (this.$refs["root"]) {
                    this.$refs["root"].rebuild(this.selected);
                }
            },

            duplicate: function (node) {
                let copy = Object.assign({}, node);
                copy.fresh = true;
                this.nodes.push(copy);
                this.select(copy);
            },

            reset: function (node) {
                if (node.fresh) {
                    return;
                }

                let index = this.nodes.indexOf(node);

                window.axios.get("frontend/behaviour_trees/" + node.id).then(response => {
                    this.nodes[index] = response.data;
                    this.select(this.nodes[index]);
                });
            },

            destroy: function (node) {
                if (!confirm("Confirm?")) {
                    return;
                }

                let index = this.nodes.indexOf(node);

                if (node.fresh) {
                    this.nodes.splice(index, 1);
                    this.select(this.nodes[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete("frontend/behaviour_trees/" + node.id).then(() => {
                    this.nodes.splice(index, 1);
                    this.select(this.nodes[index <= 0 ? 0 : index - 1]);
                });
            },

            save: function (node) {
                let index = this.nodes.indexOf(node);

                let data = this.$refs["root"].getRoot();

                if (node.fresh) {
                    window.axios.post("frontend/behaviour_trees", data).then(response => {
                        this.nodes[index] = response.data;
                        this.select(this.nodes[index]);
                    });

                    return;
                }

                window.axios.put("frontend/behaviour_trees/" + node.id, data).then(response => {
                    this.nodes[index] = response.data;
                    this.select(this.nodes[index]);
                });
            }
        }
    };
</script>
