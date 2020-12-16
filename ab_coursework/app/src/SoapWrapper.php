<?php

namespace ABCoursework;

use \SoapClient, \SoapFault;
use \Psr\Log\LoggerInterface;

/**
 * SoapWrapper: Class for connecting to a SOAP Server and calling SOAP Server functions.
 *
 * @package ABCoursework
 * @author Team AB (Jared)
 */
class SoapWrapper
{
    /**
     * @var SoapClient|null The SOAP Client connection - null if a connection hasn't been established.
     */
    private ?SoapClient $clientHandle = null;

    /**
     * @var LoggerInterface Logger used to log SOAP connections and function calls.
     */
    private LoggerInterface $logger;

    /**
     * Creates a SOAP Connection using the provided settings, logged by the provided logger.
     * @param LoggerInterface $logger Logger ued to log SOAP connections and function calls.
     * @param array $soapSettings SOAP Client connection settings e.g. ['wsdl' => 'wsdl link', 'options' => []].
     */
    public function __construct(LoggerInterface $logger, array $soapSettings)
    {
        $this->logger = $logger;
        $this->newSoapConnection($soapSettings);
    }

    /**
     * Returns the current client.
     * @return SoapClient|null Either returns a SoapClient instance or null if there's no connection.
     */
    public function getClient(): ?SoapClient
    {
        return $this->clientHandle;
    }

    /**
     * Sets SOAP Client to the given client.
     * @param SoapClient|null $client New client.
     */
    public function setClient(?SoapClient $client)
    {
        $this->clientHandle = $client;
    }

    /**
     * Sets Logger to the given logger.
     * @param LoggerInterface $logger New Logger.
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Creates a SOAP Client connection to a SOAP Service, if a connection is already made and the new connection fails
     * the old connection will persist.
     * @param array $soapSettings SOAP Client connection settings e.g. ['wsdl' => 'wsdl link', 'options' => []]
     * @return bool Whether the connection was successful.
     */
    public function newSoapConnection(array $soapSettings): bool
    {
        $success = false;

        try {
            $this->clientHandle = new SoapClient($soapSettings['wsdl'], $soapSettings['options']);
            $success = true;
            $this->logger->info('SOAP Connection Established: ', $soapSettings);
        } catch (SoapFault $exception) {
            $message = $exception->getMessage();
            $this->logger->error('SOAP Connection Error: ' . $message, $soapSettings);
        }

        return $success;
    }

    /**
     * Performs a SOAP service function call.
     * @param string $appUser Application user's username for logging function calls.
     * @param string $function SOAP Function Name.
     * @param array $params SOAP Function Parameters - Associative Array.
     * @return mixed Function result on success, null on error or null result.
     */
    public function performSoapFunction(string $appUser, string $function, array $params)
    {
        $result = null;

        if ($this->clientHandle !== null) { // Null check in case of no SOAP connection.

            // Logging Context.
            $context = ['user' => $appUser, 'function' => $function, 'params' => $params];

            // Removal of SOAP username and password from logging context so it's not logged in plain text.
            if (isset($context['params']['username'])) {
                unset($context['params']['username']);
            }

            if (isset($context['params']['password'])) {
                unset($context['params']['password']);
            }

            // Attempt to call the function on the SOAP Client and retrieve the result.
            try {
                $result = $this->clientHandle->__soapCall($function, $params);
                $this->logger->info('SOAP Function Call: ', $context);
            } catch (SoapFault $exception) {
                $message = $exception->getMessage();
                $this->logger->error('SOAP Function Error: ' . $message, $context);
            }

        }

        return $result;
    }
}