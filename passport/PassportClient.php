<?php
namespace inversoft;

/*
 * Copyright (c) 2016-2017, Inversoft Inc., All Rights Reserved
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 */

/**
 * Client that connects to a Passport server and provides access to the full set of Passport APIs.
 * <p/>
 * When any method is called the return value is always a ClientResponse object. When an API call was successful, the
 * response will contain the response from the server. This might be empty or contain an success object or an error
 * object. If there was a validation error or any other type of error, this will return the Errors object in the
 * response. Additionally, if Passport could not be contacted because it is down or experiencing a failure, the response
 * will contain an Exception, which could be an IOException.
 *
 * @author Brian Pontarelli
 */
class PassportClient
{
  /**
   * @var string
   */
  private $apiKey;

  /**
   * @var string
   */
  private $baseURL;

  /**
   * @var int
   */
  public $connectTimeout = 2000;

  /**
   * @var int
   */
  public $readTimeout = 2000;

  public function __construct($apiKey, $baseURL)
  {
    include_once 'RESTClient.php';
    $this->apiKey = $apiKey;
    $this->baseURL = $baseURL;
  }

  /**
   * Takes an action on a user. The user being actioned is called the "actionee" and the user taking the action is called the
   * "actioner". Both user ids are required. You pass the actionee's user id into the method and the actioner's is put into the
   * request object.
   *
   * @param string $actioneeUserId The actionee's user id.
   * @param array $request The action request that includes all of the information about the action being taken including
  *     the id of the action, any options and the duration (if applicable).
   *
   * @return ClientResponse The ClientResponse.
   */
  public function actionUser($actioneeUserId, $request)
  {
    return $this->start()->uri("/api/user/action")
        ->urlSegment($actioneeUserId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Cancels the user action.
   *
   * @param string $actionId The action id of the action to cancel.
   * @param array $request The action request that contains the information about the cancellation.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function cancelAction($actionId, $request)
  {
    return $this->start()->uri("/api/user/action")
        ->urlSegment($actionId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->delete()
        ->go();
  }

  /**
   * Changes a user's password using the verification id. This usually occurs after an email has been sent to the user
   * and they clicked on a link to reset their password.
   *
   * @param string $verificationId The verification id used to find the user.
   * @param array $request The change password request that contains all of the information used to change the password.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function changePassword($verificationId, $request)
  {
    return $this->start()->uri("/api/user/change-password")
        ->urlSegment($verificationId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Changes a user's password using their identity (login id and password). Using a loginId instead of the verificationId
   * bypasses the email verification and allows a password to be changed directly without first calling the #forgotPassword
   * method.
   *
   * @param array $request The change password request that contains all of the information used to change the password.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function changePasswordByIdentity($request)
  {
    return $this->start()->uri("/api/user/change-password")
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Adds a comment to the user's account.
   *
   * @param array $request The comment request that contains all of the information used to add the comment to the user.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function commentOnUser($request)
  {
    return $this->start()->uri("/api/user/comment")
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Creates an application. You can optionally specify an id for the application, but this is not required.
   *
   * @param string $applicationId (Optional) The id to use for the application.
   * @param array $request The application request that contains all of the information used to create the application.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function createApplication($applicationId, $request)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Creates a new role for an application. You must specify the id of the application you are creating the role for.
   * You can optionally specify an id for the role inside the ApplicationRole object itself, but this is not required.
   *
   * @param string $applicationId The id of the application to create the role on.
   * @param string $roleId (Optional) The id of the role. Defaults to a secure UUID.
   * @param array $request The application request that contains all of the information used to create the role.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function createApplicationRole($applicationId, $roleId, $request)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->urlSegment("role")
        ->urlSegment($roleId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Creates an audit log with the message and user name (usually an email). Audit logs should be written anytime you
   * make changes to the Passport database. When using the Passport Backend web interface, any changes are automatically
   * written to the audit log. However, if you are accessing the API, you must write the audit logs yourself.
   *
   * @param array $request The audit log request that contains all of the information used to create the audit log entry.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function createAuditLog($request)
  {
    return $this->start()->uri("/api/system/audit-log")
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Creates an email template. You can optionally specify an id for the email template when calling this method, but it
   * is not required.
   *
   * @param string $emailTemplateId (Optional) The id for the template.
   * @param array $request The email template request that contains all of the information used to create the email template.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function createEmailTemplate($emailTemplateId, $request)
  {
    return $this->start()->uri("/api/email/template")
        ->urlSegment($emailTemplateId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Creates a user with an optional id.
   *
   * @param string $userId (Optional) The id for the user.
   * @param array $request The user request that contains all of the information used to create the user.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function createUser($userId, $request)
  {
    return $this->start()->uri("/api/user")
        ->urlSegment($userId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Creates a user action. This action cannot be taken on a user until this call successfully returns. Anytime after
   * that the user action can be applied to any user.
   *
   * @param string $userActionId (Optional) The id for the user action.
   * @param array $request The user action request that contains all of the information used to create the user action.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function createUserAction($userActionId, $request)
  {
    return $this->start()->uri("/api/user-action")
        ->urlSegment($userActionId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Creates a user reason. This user action reason cannot be used when actioning a user until this call completes
   * successfully. Anytime after that the user action reason can be used.
   *
   * @param string $userActionReasonId (Optional) The id for the user action reason.
   * @param array $request The user action reason request that contains all of the information used to create the user action reason.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function createUserActionReason($userActionReasonId, $request)
  {
    return $this->start()->uri("/api/user-action-reason")
        ->urlSegment($userActionReasonId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Creates a webhook. You can optionally specify an id for the webhook when calling this method, but it is not required.
   *
   * @param string $webhookId (Optional) The id for the webhook.
   * @param array $request The webhook request that contains all of the information used to create the webhook.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function createWebhook($webhookId, $request)
  {
    return $this->start()->uri("/api/webhook")
        ->urlSegment($webhookId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Deactivates the application with the given id.
   *
   * @param string $applicationId The id of the application to deactivate.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function deactivateApplication($applicationId)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->delete()
        ->go();
  }

  /**
   * Deactivates the user with the given id.
   *
   * @param string $userId The id of the user to deactivate.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function deactivateUser($userId)
  {
    return $this->start()->uri("/api/user")
        ->urlSegment($userId)
        ->delete()
        ->go();
  }

  /**
   * Deactivates the user action with the given id.
   *
   * @param string $userActionId The id of the user action to deactivate.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function deactivateUserAction($userActionId)
  {
    return $this->start()->uri("/api/user-action")
        ->urlSegment($userActionId)
        ->delete()
        ->go();
  }

  /**
   * Deactivates the users with the given ids.
   *
   * @param array $userIds The ids of the users to deactivate.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function deactivateUsers($userIds)
  {
    return $this->start()->uri("/api/user/bulk")
        ->urlParameter("userId", $userIds)
        ->delete()
        ->go();
  }

  /**
   * Hard deletes an application. This is a dangerous operation and should not be used in most circumstances. This will
   * delete the application, any registrations for that application, metrics and reports for the application, all the
   * roles for the application, and any other data associated with the application. This operation could take a very
   * long time, depending on the amount of data in your database.
   *
   * @param string $applicationId The id of the application to delete.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function deleteApplication($applicationId)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->urlParameter("hardDelete", true)
        ->delete()
        ->go();
  }

  /**
   * Hard deletes an application role. This is a dangerous operation and should not be used in most circumstances. This
   * permanently removes the given role from all users that had it.
   *
   * @param string $applicationId The id of the application to deactivate.
   * @param string $roleId The id of the role to delete.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function deleteApplicationRole($applicationId, $roleId)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->urlSegment("role")
        ->urlSegment($roleId)
        ->delete()
        ->go();
  }

  /**
   * Deletes the email template for the given id.
   *
   * @param string $emailTemplateId The id of the email template to delete.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function deleteEmailTemplate($emailTemplateId)
  {
    return $this->start()->uri("/api/email/template")
        ->urlSegment($emailTemplateId)
        ->delete()
        ->go();
  }

  /**
   * Deletes the user registration for the given user and application.
   *
   * @param string $userId The id of the user whose registration is being deleted.
   * @param string $applicationId The id of the application to remove the registration for.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function deleteRegistration($userId, $applicationId)
  {
    return $this->start()->uri("/api/user/registration")
        ->urlSegment($userId)
        ->urlSegment($applicationId)
        ->delete()
        ->go();
  }

  /**
   * Deletes the user for the given id. This permanently deletes all information, metrics, reports and data associated
   * with the user.
   *
   * @param string $userId The id of the user to delete.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function deleteUser($userId)
  {
    return $this->start()->uri("/api/user")
        ->urlSegment($userId)
        ->urlParameter("hardDelete", true)
        ->delete()
        ->go();
  }

  /**
   * Deletes the user action for the given id. This permanently deletes the user action and also any history and logs of
   * the action being applied to any users.
   *
   * @param string $userActionId The id of the user action to delete.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function deleteUserAction($userActionId)
  {
    return $this->start()->uri("/api/user-action")
        ->urlSegment($userActionId)
        ->urlParameter("hardDelete", true)
        ->delete()
        ->go();
  }

  /**
   * Deletes the user action reason for the given id.
   *
   * @param string $userActionReasonId The id of the user action reason to delete.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function deleteUserActionReason($userActionReasonId)
  {
    return $this->start()->uri("/api/user-action-reason")
        ->urlSegment($userActionReasonId)
        ->delete()
        ->go();
  }

  /**
   * Deletes the users with the given ids.
   *
   * @param array $userIds The ids of the users to delete.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function deleteUsers($userIds)
  {
    return $this->start()->uri("/api/user/bulk")
        ->urlParameter("userId", $userIds)
        ->urlParameter("hardDelete", true)
        ->delete()
        ->go();
  }

  /**
   * Deletes the webhook for the given id.
   *
   * @param string $webhookId The id of the webhook to delete.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function deleteWebhook($webhookId)
  {
    return $this->start()->uri("/api/webhook")
        ->urlSegment($webhookId)
        ->delete()
        ->go();
  }

  /**
   * Exchange a refresh token for a new Access Token (JWT).
   *
   * @param array $request The refresh request.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function exchangeRefreshTokenForAccessToken($request)
  {
    return $this->start()->uri("/api/jwt/refresh")
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Begins the forgot password sequence, which kicks off an email to the user so that they can reset their password.
   *
   * @param array $request The request that contains the information about the user so that they can be emailed.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function forgotPassword($request)
  {
    return $this->start()->uri("/api/user/forgot-password")
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Bulk imports multiple users. This does some validation, but then tries to run batch inserts of users. This reduces
   * latency when inserting lots of users. Therefore, the error response might contain some information about failures,
   * but it will likely be pretty generic.
   *
   * @param array $request The request that contains all of the information about all of the users to import.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function importUsers($request)
  {
    return $this->start()->uri("/api/user/import")
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Issue a new access token (JWT) for the requested Application after ensuring the provided JWT is valid. A valid
   * access token is properly signed and not expired.
   * <p/>
   * This API may be used in an SSO configuration to issue new tokens for another application after the user has
   * obtained a valid token from authentication.
   *
   * @param string $applicationId The Application Id for which you are requesting a new access token be issued.
   * @param string $encodedJWT The encoded JWT (access token).
   *
   * @return ClientResponse The ClientResponse.
   */
  public function issueAccessToken($applicationId, $encodedJWT)
  {
    return $this->start()->uri("/api/jwt/issue")
        ->authorization("JWT " . $encodedJWT)
        ->urlParameter("applicationId", $applicationId)
        ->get()
        ->go();
  }

  /**
   * Logs a user in.
   *
   * @param array $request The login request that contains the user credentials used to log them in.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function login($request)
  {
    return $this->start()->uri("/api/login")
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Sends a ping to Passport indicating that the user was automatically logged into an application. When using
   * Passport's SSO or your own, you should call this if the user is already logged in centrally, but accesses an
   * application where they no longer have a session. This helps correctly track login counts, times and helps with
   * reporting.
   *
   * @param string $userId The id of the user that was logged in.
   * @param string $applicationId The id of the application that they logged into.
   * @param string $callerIPAddress (Optional) The IP address of the end-user that is logging in. If a null value is provided
  *     the IP address will be that of the client or last proxy that sent the request.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function loginPing($userId, $applicationId, $callerIPAddress)
  {
    return $this->start()->uri("/api/login")
        ->urlSegment($userId)
        ->urlSegment($applicationId)
        ->urlParameter("ipAddress", $callerIPAddress)
        ->put()
        ->go();
  }

  /**
   * The Logout API is intended to be used to remove the refresh token and access token cookies if they exist on the
   * client and revoke the refresh token stored. This API does nothing if the request does not contain an access
   * token or refresh token cookies.
   *
   * @param array $global (Optional) When this value is set to true all of the refresh tokens issued to the owner of the
  *     provided token will be revoked.
   * @param string $refreshToken (Optional) The refresh_token as a request parameter instead of coming in via a cookie.
  *     If provided this takes precedence over the cookie.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function logout($global, $refreshToken)
  {
    return $this->start()->uri("/api/logout")
        ->urlParameter("global", $global)
        ->urlParameter("refreshToken", $refreshToken)
        ->post()
        ->go();
  }

  /**
   * Modifies a temporal user action by changing the expiration of the action and optionally adding a comment to the
   * action.
   *
   * @param string $actionId The id of the action to modify. This is technically the user action log id.
   * @param array $request The request that contains all of the information about the modification.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function modifyAction($actionId, $request)
  {
    return $this->start()->uri("/api/user/action")
        ->urlSegment($actionId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->put()
        ->go();
  }

  /**
   * Reactivates the application with the given id.
   *
   * @param string $applicationId The id of the application to reactivate.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function reactivateApplication($applicationId)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->urlParameter("reactivate", true)
        ->put()
        ->go();
  }

  /**
   * Reactivates the user with the given id.
   *
   * @param string $userId The id of the user to reactivate.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function reactivateUser($userId)
  {
    return $this->start()->uri("/api/user")
        ->urlSegment($userId)
        ->urlParameter("reactivate", true)
        ->put()
        ->go();
  }

  /**
   * Reactivates the user action with the given id.
   *
   * @param string $userActionId The id of the user action to reactivate.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function reactivateUserAction($userActionId)
  {
    return $this->start()->uri("/api/user-action")
        ->urlSegment($userActionId)
        ->urlParameter("reactivate", true)
        ->put()
        ->go();
  }

  /**
   * Registers a user for an application. If you provide the User and the UserRegistration object on this request, it
   * will create the user as well as register them for the application. This is called a Full Registration. However, if
   * you only provide the UserRegistration object, then the user must already exist and they will be registered for the
   * application. The user id can also be provided and it will either be used to look up an existing user or it will be
   * used for the newly created User.
   *
   * @param string $userId (Optional) The id of the user being registered for the application and optionally created.
   * @param array $request The request that optionally contains the User and must contain the UserRegistration.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function register($userId, $request)
  {
    return $this->start()->uri("/api/user/registration")
        ->urlSegment($userId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Re-sends the verification email to the user.
   *
   * @param string $email The email address of the user that needs a new verification email.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function resendEmailVerification($email)
  {
    return $this->start()->uri("/api/user/verify-email")
        ->urlParameter("email", $email)
        ->put()
        ->go();
  }

  /**
   * Retrieves a single action log (the log of a user action that was taken on a user previously) for the given id.
   *
   * @param string $actionId The id of the action to retrieve.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveAction($actionId)
  {
    return $this->start()->uri("/api/user/action")
        ->urlSegment($actionId)
        ->get()
        ->go();
  }

  /**
   * Retrieves all of the actions for the user with the given id.
   *
   * @param string $userId The id of the user to fetch the actions for.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveActions($userId)
  {
    return $this->start()->uri("/api/user/action")
        ->urlParameter("userId", $userId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the application for the given id or all of the applications if the id is null.
   *
   * @param string $applicationId (Optional) The application id.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveApplication($applicationId)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->get()
        ->go();
  }

  /**
   * Retrieves all of the applications.
   *
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveApplications()
  {
    return $this->start()->uri("/api/application")
        ->get()
        ->go();
  }

  /**
   * Retrieves a single audit log for the given id.
   *
   * @param array $auditLogId The id of the audit log to retrieve.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveAuditLog($auditLogId)
  {
    return $this->start()->uri("/api/system/audit-log")
        ->urlSegment($auditLogId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the daily active user report between the two instants. If you specify an application id, it will only
   * return the daily active counts for that application.
   *
   * @param string $applicationId (Optional) The application id.
   * @param array $start The start instant as UTC milliseconds since Epoch.
   * @param array $end The end instant as UTC milliseconds since Epoch.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveDailyActiveReport($applicationId, $start, $end)
  {
    return $this->start()->uri("/api/report/daily-active-user")
        ->urlParameter("applicationId", $applicationId)
        ->urlParameter("start", $start)
        ->urlParameter("end", $end)
        ->get()
        ->go();
  }

  /**
   * Retrieves the email template for the given id. If you don't specify the id, this will return all of the email templates.
   *
   * @param string $emailTemplateId (Optional) The id of the email template.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveEmailTemplate($emailTemplateId)
  {
    return $this->start()->uri("/api/email/template")
        ->urlSegment($emailTemplateId)
        ->get()
        ->go();
  }

  /**
   * Creates a preview of the email template provided in the request. This allows you to preview an email template that
   * hasn't been saved to the database yet. The entire email template does not need to be provided on the request. This
   * will create the preview based on whatever is given.
   *
   * @param array $request The request that contains the email template and optionally a locale to render it in.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveEmailTemplatePreview($request)
  {
    return $this->start()->uri("/api/email/template/preview")
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Retrieves all of the email templates.
   *
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveEmailTemplates()
  {
    return $this->start()->uri("/api/email/template")
        ->get()
        ->go();
  }

  /**
   * Retrieves all of the applications that are currently inactive.
   *
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveInactiveApplications()
  {
    return $this->start()->uri("/api/application")
        ->urlParameter("inactive", true)
        ->get()
        ->go();
  }

  /**
   * Retrieves all of the user actions that are currently inactive.
   *
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveInactiveUserActions()
  {
    return $this->start()->uri("/api/user-action")
        ->urlParameter("inactive", true)
        ->get()
        ->go();
  }

  /**
   * Retrieves the Public Key configured for verifying JSON Web Tokens (JWT) by the key Id. If the key Id is provided a
   * single public key will be returned if one is found by that id. If the optional parameter key Id is not provided all
   * public keys will be returned.
   *
   * @param string $keyId (Optional) The id of the public key.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveJWTPublicKey($keyId)
  {
    return $this->start()->uri("/api/jwt/public-key")
        ->urlSegment($keyId)
        ->get()
        ->go();
  }

  /**
   * Retrieves all Public Keys configured for verifying JSON Web Tokens (JWT).
   *
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveJWTPublicKeys()
  {
    return $this->start()->uri("/api/jwt/public-key")
        ->get()
        ->go();
  }

  /**
   * Retrieves the login report between the two instants. If you specify an application id, it will only return the
   * login counts for that application.
   *
   * @param string $applicationId (Optional) The application id.
   * @param array $start The start instant as UTC milliseconds since Epoch.
   * @param array $end The end instant as UTC milliseconds since Epoch.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveLoginReport($applicationId, $start, $end)
  {
    return $this->start()->uri("/api/report/login")
        ->urlParameter("applicationId", $applicationId)
        ->urlParameter("start", $start)
        ->urlParameter("end", $end)
        ->get()
        ->go();
  }

  /**
   * Retrieves the monthly active user report between the two instants. If you specify an application id, it will only
   * return the monthly active counts for that application.
   *
   * @param string $applicationId (Optional) The application id.
   * @param array $start The start instant as UTC milliseconds since Epoch.
   * @param array $end The end instant as UTC milliseconds since Epoch.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveMonthlyActiveReport($applicationId, $start, $end)
  {
    return $this->start()->uri("/api/report/monthly-active-user")
        ->urlParameter("applicationId", $applicationId)
        ->urlParameter("start", $start)
        ->urlParameter("end", $end)
        ->get()
        ->go();
  }

  /**
   * Retrieves the refresh tokens that belong to the user with the given id.
   *
   * @param string $userId The id of the user.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveRefreshTokens($userId)
  {
    return $this->start()->uri("/api/jwt/refresh")
        ->urlParameter("userId", $userId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the user registration for the user with the given id and the given application id.
   *
   * @param string $userId The id of the user.
   * @param string $applicationId The id of the application.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveRegistration($userId, $applicationId)
  {
    return $this->start()->uri("/api/user/registration")
        ->urlSegment($userId)
        ->urlSegment($applicationId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the registration report between the two instants. If you specify an application id, it will only return
   * the registration counts for that application.
   *
   * @param string $applicationId (Optional) The application id.
   * @param array $start The start instant as UTC milliseconds since Epoch.
   * @param array $end The end instant as UTC milliseconds since Epoch.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveRegistrationReport($applicationId, $start, $end)
  {
    return $this->start()->uri("/api/report/registration")
        ->urlParameter("applicationId", $applicationId)
        ->urlParameter("start", $start)
        ->urlParameter("end", $end)
        ->get()
        ->go();
  }

  /**
   * Retrieves the system configuration.
   *
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveSystemConfiguration()
  {
    return $this->start()->uri("/api/system-configuration")
        ->get()
        ->go();
  }

  /**
   * Retrieves the totals report. This contains all of the total counts for each application and the global registration
   * count.
   *
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveTotalReport()
  {
    return $this->start()->uri("/api/report/totals")
        ->get()
        ->go();
  }

  /**
   * Retrieves the user for the given id.
   *
   * @param string $userId The id of the user.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveUser($userId)
  {
    return $this->start()->uri("/api/user")
        ->urlSegment($userId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the user action for the given id. If you pass in null for the id, this will return all of the user
   * actions.
   *
   * @param string $userActionId (Optional) The id of the user action.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveUserAction($userActionId)
  {
    return $this->start()->uri("/api/user-action")
        ->urlSegment($userActionId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the user action reason for the given id. If you pass in null for the id, this will return all of the user
   * action reasons.
   *
   * @param string $userActionReasonId (Optional) The id of the user action reason.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveUserActionReason($userActionReasonId)
  {
    return $this->start()->uri("/api/user-action-reason")
        ->urlSegment($userActionReasonId)
        ->get()
        ->go();
  }

  /**
   * Retrieves all the user action reasons.
   *
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveUserActionReasons()
  {
    return $this->start()->uri("/api/user-action-reason")
        ->get()
        ->go();
  }

  /**
   * Retrieves all of the user actions.
   *
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveUserActions()
  {
    return $this->start()->uri("/api/user-action")
        ->get()
        ->go();
  }

  /**
   * Retrieves the user for the given email.
   *
   * @param string $email The email of the user.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveUserByEmail($email)
  {
    return $this->start()->uri("/api/user")
        ->urlParameter("email", $email)
        ->get()
        ->go();
  }

  /**
   * Retrieves the user for the loginId. The loginId can be either the username or the email.
   *
   * @param string $loginId The email or username of the user.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveUserByLoginId($loginId)
  {
    return $this->start()->uri("/api/user")
        ->urlParameter("loginId", $loginId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the user for the given username.
   *
   * @param string $username The username of the user.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveUserByUsername($username)
  {
    return $this->start()->uri("/api/user")
        ->urlParameter("username", $username)
        ->get()
        ->go();
  }

  /**
   * Retrieves all of the comments for the user with the given id.
   *
   * @param string $userId The id of the user.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveUserComments($userId)
  {
    return $this->start()->uri("/api/user/comment")
        ->urlSegment($userId)
        ->get()
        ->go();
  }

  /**
   * Retrieves the last number of login records for a user.
   *
   * @param string $userId The id of the user.
   * @param array $offset The initial record. e.g. 0 is the last login, 100 will be the 100th most recent login.
   * @param array $limit (Optional, defaults to 10) The number of records to retrieve.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveUserLoginReport($userId, $offset, $limit)
  {
    return $this->start()->uri("/api/report/user-login")
        ->urlParameter("userId", $userId)
        ->urlParameter("offset", $offset)
        ->urlParameter("limit", $limit)
        ->get()
        ->go();
  }

  /**
   * Retrieves the webhook for the given id. If you pass in null for the id, this will return all the webhooks.
   *
   * @param string $webhookId (Optional) The id of the webhook.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveWebhook($webhookId)
  {
    return $this->start()->uri("/api/webhook")
        ->urlSegment($webhookId)
        ->get()
        ->go();
  }

  /**
   * Retrieves all the webhooks.
   *
   *
   * @return ClientResponse The ClientResponse.
   */
  public function retrieveWebhooks()
  {
    return $this->start()->uri("/api/webhook")
        ->get()
        ->go();
  }

  /**
   * Searches the audit logs with the specified criteria and pagination.
   *
   * @param array $request The search criteria and pagination information.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function searchAuditLogs($request)
  {
    return $this->start()->uri("/api/system/audit-log/search")
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Retrieves the users for the given ids. If any id is invalid, it is ignored.
   *
   * @param array $ids The user ids to search for.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function searchUsers($ids)
  {
    return $this->start()->uri("/api/user/search")
        ->urlParameter("ids", $ids)
        ->get()
        ->go();
  }

  /**
   * Retrieves the users for the given search criteria and pagination.
   *
   * @param array $request The search criteria and pagination constraints. Fields used: queryString, numberOfResults, startRow,
  *     and sort fields.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function searchUsersByQueryString($request)
  {
    return $this->start()->uri("/api/user/search")
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Send an email using an email template id. You can optionally provide <code>requestData</code> to access key value
   * pairs in the email template.
   *
   * @param string $emailTemplateId The id for the template.
   * @param array $request The send email request that contains all of the information used to send the email.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function sendEmail($emailTemplateId, $request)
  {
    return $this->start()->uri("/api/email/send")
        ->urlSegment($emailTemplateId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  /**
   * Updates the application with the given id.
   *
   * @param string $applicationId The id of the application to update.
   * @param array $request The request that contains all of the new application information.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function updateApplication($applicationId, $request)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->put()
        ->go();
  }

  /**
   * Updates the application role with the given id for the application.
   *
   * @param string $applicationId The id of the application that the role belongs to.
   * @param string $roleId The id of the role to update.
   * @param array $request The request that contains all of the new role information.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function updateApplicationRole($applicationId, $roleId, $request)
  {
    return $this->start()->uri("/api/application")
        ->urlSegment($applicationId)
        ->urlSegment("role")
        ->urlSegment($roleId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->put()
        ->go();
  }

  /**
   * Updates the email template with the given id.
   *
   * @param string $emailTemplateId The id of the email template to update.
   * @param array $request The request that contains all of the new email template information.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function updateEmailTemplate($emailTemplateId, $request)
  {
    return $this->start()->uri("/api/email/template")
        ->urlSegment($emailTemplateId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->put()
        ->go();
  }

  /**
   * Updates the registration for the user with the given id and the application defined in the request.
   *
   * @param string $userId The id of the user whose registration is going to be updated.
   * @param array $request The request that contains all of the new registration information.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function updateRegistration($userId, $request)
  {
    return $this->start()->uri("/api/user/registration")
        ->urlSegment($userId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->put()
        ->go();
  }

  /**
   * Updates the system configuration.
   *
   * @param array $request The request that contains all of the new system configuration information.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function updateSystemConfiguration($request)
  {
    return $this->start()->uri("/api/system-configuration")
        ->bodyHandler(new JSONBodyHandler($request))
        ->put()
        ->go();
  }

  /**
   * Updates the user with the given id.
   *
   * @param string $userId The id of the user to update.
   * @param array $request The request that contains all of the new user information.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function updateUser($userId, $request)
  {
    return $this->start()->uri("/api/user")
        ->urlSegment($userId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->put()
        ->go();
  }

  /**
   * Updates the user action with the given id.
   *
   * @param string $userActionId The id of the user action to update.
   * @param array $request The request that contains all of the new user action information.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function updateUserAction($userActionId, $request)
  {
    return $this->start()->uri("/api/user-action")
        ->urlSegment($userActionId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->put()
        ->go();
  }

  /**
   * Updates the user action reason with the given id.
   *
   * @param string $userActionReasonId The id of the user action reason to update.
   * @param array $request The request that contains all of the new user action reason information.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function updateUserActionReason($userActionReasonId, $request)
  {
    return $this->start()->uri("/api/user-action-reason")
        ->urlSegment($userActionReasonId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->put()
        ->go();
  }

  /**
   * Updates the webhook with the given id.
   *
   * @param string $webhookId The id of the webhook to update.
   * @param array $request The request that contains all of the new webhook information.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function updateWebhook($webhookId, $request)
  {
    return $this->start()->uri("/api/webhook")
        ->urlSegment($webhookId)
        ->bodyHandler(new JSONBodyHandler($request))
        ->put()
        ->go();
  }

  /**
   * Validates the provided JWT (encoded JWT string) to ensure the token is valid. A valid access token is properly
   * signed and not expired.
   * <p/>
   * This API may be used to verify the JWT as well as decode the encoded JWT into human readable identity claims.
   *
   * @param string $encodedJWT The encoded JWT (access token).
   *
   * @return ClientResponse The ClientResponse.
   */
  public function validateAccessToken($encodedJWT)
  {
    return $this->start()->uri("/api/jwt/validate")
        ->authorization("JWT" . $encodedJWT)
        ->get()
        ->go();
  }

  /**
   * Confirms a email verification. The id given is usually from an email sent to the user.
   *
   * @param string $verificationId The verification id sent to the user.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function verifyEmail($verificationId)
  {
    return $this->start()->uri("/api/user/verify-email")
        ->urlSegment($verificationId)
        ->post()
        ->go();
  }

  /**
   * Confirms a two factor authentication code.
   *
   * @param array $request The two factor request information.
   *
   * @return ClientResponse The ClientResponse.
   */
  public function verifyTwoFactor($request)
  {
    return $this->start()->uri("/api/two-factor")
        ->bodyHandler(new JSONBodyHandler($request))
        ->post()
        ->go();
  }

  private function start()
  {
    $rest = new RESTClient();
    return $rest->authorization($this->apiKey)
        ->url($this->baseURL)
        ->connectTimeout($this->connectTimeout)
        ->readTimeout($this->readTimeout)
        ->successResponseHandler(new JSONResponseHandler())
        ->errorResponseHandler(new JSONResponseHandler());
  }
}