/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application

import './bootstrap';

/*
* Added By Ours
*/
// nos travail
/*
const addFormToCollection = (e) => {
        const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

        const item = document.createElement('div');

        item.innerHTML = collectionHolder
            .dataset
            .prototype
            .replace(
                /__name__/g,
                collectionHolder.dataset.index
            );

        collectionHolder.appendChild(item);

        collectionHolder.dataset.index++;
};
document
    .querySelectorAll('.add_item_link')
    .forEach(btn => {
            btn.addEventListener("click", addFormToCollection)
    });
*/

//end nos travail


import React from "react";
import { StrictMode } from "react";
import { createRoot } from 'react-dom/client';
import { QueryClient, QueryClientProvider } from "react-query";
const queryClient = new QueryClient();


import MyDataTable from "./components/MyDataTable";


const dtables = ['dt1','dt2','dt3', 'dt4', 'dt5'];

for (let i = 0; i < dtables.length; i++) {

        const container = document.getElementById(dtables[i]);

        if(container!=null){
                const dt_selectableRows = container.getAttribute('dt_selectableRows');
                const dt_title = container.getAttribute('dt_title');
                const API_URL = window.location.origin+'/admin/dt_api/'+ container.getAttribute('entity') + window.location.search;
                const location_action_route = window.location.origin + container.getAttribute('action_route');

                const root = createRoot(container); // createRoot(container!) if you use TypeScript
                root.render(<StrictMode>
                        <QueryClientProvider client={queryClient}>
                                <MyDataTable
                                    data_route = {API_URL}
                                    action_route = {location_action_route}
                                    title = {dt_title}
                                    selectableRows = {dt_selectableRows}
                                    dropdownBtn = 'true'
                                    expandOnRowClicked = 'true'
                                />
                        </QueryClientProvider>
                </StrictMode>);
        }
}
