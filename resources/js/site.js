import Alpine from "alpinejs";
import { initOnThisPage } from "./components/onThisPage";
import { setupSidebarHeights } from "./components/sidebarHeights";
import { initSyntaxHighlighting } from "./components/syntaxHighlighting";
import { initVersionSelection } from "./components/versionSelection";
import { siteData } from "./components/siteData";
import { collapsibleList } from "./components/collapsibleList";
import { dropdownMenu } from "./components/dropdownMenu";
import { initSearch } from "./components/search";
import { initLocalePicker } from "./components/localePicker";

window.Alpine = Alpine;

initOnThisPage();
initVersionSelection();
initLocalePicker();
setupSidebarHeights();
initSyntaxHighlighting();
initSearch();

window.siteData = siteData;
window.collapsibleList = collapsibleList;
window.dropdownMenu = dropdownMenu;

Alpine.start();
