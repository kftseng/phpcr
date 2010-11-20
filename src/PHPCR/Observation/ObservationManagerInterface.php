<?php
/**
 * Interface description of how to implement an observation manager.
 *
 * This file was ported from the Java JCR API to PHP by
 * Karsten Dambekalns <karsten@typo3.org> for the FLOW3 project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version. Alternatively, you may use the Simplified
 * BSD License.
 *
 * This script is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser
 * General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with the script.
 * If not, see {@link http://www.gnu.org/licenses/lgpl.html}.
 *
 * The TYPO3 project - inspiring people to share!
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @license http://opensource.org/licenses/bsd-license.php Simplified BSD License
 *
 * @package phpcr
 * @subpackage interfaces
 */

declare(ENCODING = 'utf-8');
namespace PHPCR;

/**
 * The ObservationManager object.
 *
 * Acquired via Workspace.getObservationManager(). Allows for the registration
 * and deregistration of event listeners.
 *
 * @package phpcr
 * @subpackage interfaces
 * @api
 */
interface ObservationManagerInterface {

    /**
     * Adds an event listener that listens for the specified eventTypes (a
     * combination of one or more event types encoded as a bit mask value).
     * The set of events will be further filtered by the access rights of the c
     * urrent Session as well as the restrictions specified by the parameters of
     * this method. These restrictions are stated in terms of characteristics of the
     * associated parent node of the event.
     *
     * The associated parent node of an event is the parent node of the item at (or
     * formerly at) the path returned by Event.getPath(). The following restrictions
     * are available:
     *  <b>absPath</b>, <b>isDeep</b>: Only events whose associated parent node is at absPath (or
     *   within its subgraph, if isDeep is true) will be received. It is permissible
     *   to register a listener for a path where no node currently exists.
     *  <b>uuid</b>: Only events whose associated parent node has one of the identifiers in
     *   this list will be received. If his parameter is null then no identifier-
     *   related restriction is placed on events received. Note that specifying an
     *   empty array instead of null would result in no nodes being listened to. The
     *   term "UUID" is used for compatibility with JCR 1.0.
     *  <b>nodeTypeName</b>: Only events whose associated parent node has one of the node
     *   types (or a subtype of one of the node types) in this list will be received.
     *   If his parameter is null then no node type-related restriction is placed on
     *   events received. Note that specifying an empty array instead of null would
     *   result in no nodes types being listened to.
     *
     * The restrictions are "ANDed" together. In other words, for a particular node
     * to be "listened to" it must meet all the restrictions.
     * Additionally, if noLocal is true, then events generated by the session
     * through which the listener was registered are ignored. Otherwise, they are
     * not ignored.
     *
     * The filters of an already-registered EventListener can be changed at runtime
     * by re-registering the same EventListener object (i.e. the same actual object)
     * with a new set of filter arguments. The implementation must ensure that no
     * events are lost during the changeover.
     *
     * In addition to the filters placed on a listener above, the scope of
     * observation support, in terms of which subgraphs are observable, may also be
     * subject to implementation-specific restrictions. For example, in some
     * repositories observation of changes in the jcr:system subgraph may not be
     * supported
     *
     * @param \PHPCR\Observation\EventListenerInterface $listener An EventListener object.
     * @param integer $eventTypes A combination of one or more event type constants encoded as a bitmask.
     * @param string $absPath The absolute path identifying the node to be observed.
     * @param boolean $isDeep Switch to define the given path as a reference to a child node.
     * @param array $uuid List of identifiers of events to be recieved.
     * @param array $nodeTypeName List of node type names to identify the events to be recieved.
     * @param boolean $noLocal switch to handle local events.
     * @return void
     *
     * @throws \PHPCR\RepositoryException if an error occurs.
     * @api
     */
    public function addEventListener(\PHPCR\Observation\EventListenerInterface $listener, $eventTypes, $absPath,
                                     $isDeep, array $uuid, array $nodeTypeName, $noLocal);

    /**
     * Deregisters an event listener.
     *
     * A listener may be deregistered while it is being executed. The deregistration
     * method will block until the listener has completed executing. An exception to
     * this rule is a listener which deregisters itself from within the onEvent
     * method. In this case, the deregistration method returns immediately, but
     * deregistration will effectively be delayed until the listener completes.
     *
     * @param \PHPCR\Observation\EventListenerInterface $listener The listener to deregister.
     * @return void
     *
     * @throws \PHPCR\RepositoryException if an error occurs.
     * @api
     */
    public function removeEventListener(\PHPCR\Observation\EventListenerInterface $listener);

    /**
     * Returns all event listeners that have been registered through this session.
     *
     * If no listeners have been registered, an empty iterator is returned.
     *
     * @return Iterator implementing <b>SeekableIterator</b> and <b>Countable</b>.
     *                  Values are the EventListenerInterface instances. Keys have no meaning.
     *
     * @throws \PHPCR\RepositoryException if an error occurs
     * @api
     */
    public function getRegisteredEventListeners();

    /**
     * Sets the user data information that will be returned by Event.getUserData().
     *
     * @param string $userData the user data
     * @return void
     *
     * @throws \PHPCR\RepositoryException if an error occurs
     * @api
     */
    public function setUserData($userData);

    /**
     * Retrieves the event journal for this workspace.
     *
     * If journaled observation is not supported for this workspace, NULL is
     * returned.
     *
     * Events returned in the EventJournal instance will be filtered according
     * to the parameters of this method, the current session's access
     * restrictions as well as any additional restrictions specified through
     * implemention-specific configuration.
     *
     * The parameters of this method filter the event set in the same way as
     * they do in addEventListener().
     *
     * @param integer $eventTypes A combination of one or more event type constants encoded as a bitmask.
     * @param string $absPath an absolute path.
     * @param boolean $isDeep Switch to define the given path as a reference to a child node.
     * @param array $uuid array of identifiers.
     * @param array $nodeTypeName array of node type names.
     * @return \PHPCR\Observation\EventJournalInterface an EventJournal (or NULL).
     *
     * @throws \PHPCR\RepositoryException if an error occurs
     * @api
     */
    public function getEventJournal($eventTypes = NULL, $absPath = NULL, $isDeep = NULL, array $uuid = NULL,
                                    array $nodeTypeName = NULL);

}
