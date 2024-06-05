import { defineStore } from 'pinia';
import { nanoid } from 'nanoid';

export const useBusy = defineStore('busy', {
    state: () => ({
        tasks: []
    }),
    actions: {
        /**
         * @param {string} task 
         */
        add(task = null) {
            const id = nanoid();
            this.tasks.unshift({
                timestamp: Date.now(),
                task: task,
                id: id,
            });
            return id;
        },
        /**
         * @param {string} task 
         */
        remove(search = null) {
            // search the task by id, if not found, search by task name
            const index = this.tasks.findIndex((t) => t.id === search);
            if (index !== -1) {
                this.tasks.splice(index, 1);
                return true;
            }

            let found = false;
            this.tasks = this.tasks.filter((t) => {
                if (found) {
                    return true;
                }
                if (t.task === search) {
                    found = true;
                    return false;
                }
                return true;
            });
        },
        reset() {
            this.tasks = [];
        }
    },
    getters: {
        /**
         * @returns {boolean}
         */
        isBusy: (state) => state.tasks.length > 0,
        /**
         * @param {string} task
         * @returns {Function}
         */
        hasTask: (state) => {
            return (task) => state.tasks.some((t) => t.task === task);
        },
    },
});