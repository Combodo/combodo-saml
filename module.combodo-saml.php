<?php
/**
 * @copyright   Copyright (C) 2010-2019 Combodo SARL
 * @license     https://www.combodo.com/documentation/combodo-software-license.html
 *
 */
//
// iTop module definition file
//

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'combodo-saml/1.0.0',
	array(
		// Identification
		//
		'label' => 'SAML SSO',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			
		),
		'mandatory' => false,
		'visible' => true,

		// Components
		//
		'datamodel' => array(
			'model.combodo-saml.php',
			'main.php',
		),
		'webservice' => array(
			
		),
		'data.struct' => array(
			// add your 'structure' definition XML files here,
		),
		'data.sample' => array(
			// add your sample data XML files here,
		),
		
		// Documentation
		//
		'doc.manual_setup' => '', // hyperlink to manual setup documentation, if any
		'doc.more_information' => '', // hyperlink to more information, if any 

		// Default settings
		//
		'settings' => array(
			// If 'strict' is True, then the PHP Toolkit will reject unsigned
			// or unencrypted messages if it expects them to be signed or encrypted.
			// Also it will reject the messages if the SAML standard is not strictly
			// followed: Destination, NameId, Conditions ... are validated too.
			'strict' => true,
			// Enable debug mode (to print errors).
			'debug' => true,
			// Service Provider Data that we are deploying.
			'sp' => array (
				// Specifies info about where and how the <AuthnResponse> message MUST be
				// returned to the requester, in this case our SP.
				'assertionConsumerService' => array (
					// SAML protocol binding to be used when returning the <Response>
					// message. OneLogin Toolkit supports this endpoint for the
					// HTTP-POST binding only.
					'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
				),
				// If you need to specify requested attributes, set a
				// attributeConsumingService. nameFormat, attributeValue and
				// friendlyName can be omitted
				"attributeConsumingService"=> array(
					"serviceName" => "SP test",
					"serviceDescription" => "Test Service",
					"requestedAttributes" => array(
						array(
							"name" => "",
							"isRequired" => false,
							"nameFormat" => "",
							"friendlyName" => "",
							"attributeValue" => array()
						)
					)
				),
				// Specifies info about where and how the <Logout Response> message MUST be
				// returned to the requester, in this case our SP.
				'singleLogoutService' => array (
					// SAML protocol binding to be used when returning the <Response>
					// message. OneLogin Toolkit supports the HTTP-Redirect binding
					// only for this endpoint.
					'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
				),
				// Specifies the constraints on the name identifier to be used to
				// represent the requested subject.
				// Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported.
				'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
				// Usually x509cert and privateKey of the SP are provided by files placed at
				// the certs folder. But we can also provide them with the following parameters
				'x509cert' => '',
				'privateKey' => '',
				/*
				 * Key rollover
				 * If you plan to update the SP x509cert and privateKey
				 * you can define here the new x509cert and it will be
				 * published on the SP metadata so Identity Providers can
				 * read them and get ready for rollover.
				 */
				// 'x509certNew' => '',

			),

			// Identity Provider Data that we want connected with our SP.
			'idp' => array (
				// Identifier of the IdP entity  (must be a URI)
				'entityId' => '',
				// SSO endpoint info of the IdP. (Authentication Request protocol)
				'singleSignOnService' => array (
					// URL Target of the IdP where the Authentication Request Message
					// will be sent.
					'url' => '',
					// SAML protocol binding to be used when returning the <Response>
					// message. OneLogin Toolkit supports the HTTP-Redirect binding
					// only for this endpoint.
					'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
				),
				// SLO endpoint info of the IdP.
				'singleLogoutService' => array (
					// URL Location of the IdP where SLO Request will be sent.
					'url' => 'https://customers.combodo.com/simplesaml/saml2/idp/SingleLogoutService.php',
					// URL location of the IdP where the SP will send the SLO Response (ResponseLocation)
					// if not set, url for the SLO Request will be used
					'responseUrl' => '',
					// SAML protocol binding to be used when returning the <Response>
					// message. OneLogin Toolkit supports the HTTP-Redirect binding
					// only for this endpoint.
					'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
				),
				// Public x509 certificate of the IdP
				'x509cert' => '',
				/*
				 *  Instead of use the whole x509cert you can use a fingerprint in order to
				 *  validate a SAMLResponse, but we don't recommend to use that
				 *  method on production since is exploitable by a collision attack.
				 *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it,
				 *   or add for example the -sha256 , -sha384 or -sha512 parameter)
				 *
				 *  If a fingerprint is provided, then the certFingerprintAlgorithm is required in order to
				 *  let the toolkit know which algorithm was used. Possible values: sha1, sha256, sha384 or sha512
				 *  'sha1' is the default value.
				 *
				 *  Notice that if you want to validate any SAML Message sent by the HTTP-Redirect binding, you
				 *  will need to provide the whole x509cert.
				 */
				// 'certFingerprint' => '',
				// 'certFingerprintAlgorithm' => 'sha1',

				/* In some scenarios the IdP uses different certificates for
				 * signing/encryption, or is under key rollover phase and
				 * more than one certificate is published on IdP metadata.
				 * In order to handle that the toolkit offers that parameter.
				 * (when used, 'x509cert' and 'certFingerprint' values are
				 * ignored).
				 */
				// 'x509certMulti' => array(
				//      'signing' => array(
				//          0 => '<cert1-string>',
				//      ),
				//      'encryption' => array(
				//          0 => '<cert2-string>',
				//      )
				// ),
			),
		),
	)
);

