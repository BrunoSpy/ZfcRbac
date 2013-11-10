<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ZfcRbac\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Exception;
use ZfcRbac\Permission\ObjectRepositoryPermissionProvider;

/**
 * Factory used to create an object repository permission provider
 */
class ObjectRepositoryPermissionProviderFactory implements FactoryInterface, MutableCreationOptionsInterface
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * {@inheritDoc}
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     * @return ObjectRepositoryPermissionProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $parentLocator    = $serviceLocator->getServiceLocator();
        $objectRepository = null;

        if (isset($this->options['object_repository'])) {
            $objectRepository = $parentLocator->get($this->options['object_repository']);
        } elseif (isset($this->options['object_manager']) && isset($this->options['class_name'])) {
            /* @var \Doctrine\Common\Persistence\ObjectManager $objectManager */
            $objectManager    = $parentLocator->get($this->options['object_manager']);
            $objectRepository = $objectManager->getRepository($this->options['class_name']);
        }

        if (null === $objectRepository) {
            throw new Exception\RuntimeException(
                'No object repository was found while creating the ZfcRbac object repository permission provider. Are
                 you sure you specified either the object_repository option or object_manager/class_name options?'
            );
        }

        return new ObjectRepositoryPermissionProvider($objectRepository);
    }
}