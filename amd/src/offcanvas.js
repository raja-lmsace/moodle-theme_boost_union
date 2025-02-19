// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme Boost Union - JS code off-canvas
 *
 * @module     theme_boost_union/offcanvas
 * @copyright  2022 bdecent gmbh <https://bdecent.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'theme_boost/drawers', 'core/modal'], function($, Drawers, Modal) {

    let modalBackdrop = null;

    const getDrawerBackdrop = function() {
        if (!modalBackdrop) {
             modalBackdrop = Modal.prototype.getBackdrop().then(backdrop => {
                backdrop.getAttachmentPoint().get(0).addEventListener('click', e => {
                    e.preventDefault();
                    var currentDrawer = Drawers.getDrawerInstanceForNode(
                        document.getElementById('theme_boost_union-drawers-offcanvas')
                    );
                    currentDrawer.closeDrawer(false);
                    backdrop.hide();
                });
                return backdrop;
            })
            .catch();
        }
        return modalBackdrop;
    };

    /**
     * Used this listener to hide the off canvas drawer from the page.
     */
    function initOffCanvasEventListeners() {
        document.addEventListener(Drawers.eventTypes.drawerShown, function(e) {
            if (e.target.id != 'theme_boost_union-drawers-offcanvas') {
                return null;
            }
            getDrawerBackdrop().then(backdrop => {
                backdrop.show();
                $('body').addClass('top-offcanvas-drawer');
                return backdrop;
            })
            .catch();

            return true;
        });

        document.addEventListener(Drawers.eventTypes.drawerHide, function() {
            getDrawerBackdrop().then(backdrop => {
                backdrop.hide();
                $('body').removeClass('top-offcanvas-drawer');
                return;
            })
            .catch();
        });

        // Display the offcanvas block in top of the header when the turn editing mode is on.
        $('#theme_boost_union-offcanvas-btn').click(function() {
            $('#theme_boost_union-offcanvas').toggleClass('show');
        });
    }

    return {
        init: function() {
            initOffCanvasEventListeners();
        }
    };

});
