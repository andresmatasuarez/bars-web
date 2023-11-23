import React from "react";
import { createRoot } from "react-dom/client";
import App from "./App";
import DataProvider from "./data/DataProvider";

const root = createRoot(document.getElementById("react-root-selection")!);
root.render(
  <DataProvider>
    <App />
  </DataProvider>
);
