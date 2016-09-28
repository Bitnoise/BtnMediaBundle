<?php

namespace Btn\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class UrlResolverTypeHandlerCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $urlResolverId = 'btn_media.url.resolver';

        if (!$container->hasDefinition($urlResolverId)) {
            return;
        }

        $urlResolver = $container->getDefinition($urlResolverId);

        $typeHandlers = $container->findTaggedServiceIds('btn_media.url_resolver_type_handler');
        if (!$typeHandlers) {
            return;
        }

        foreach ($typeHandlers as $id => $typeHandlersTags) {
            foreach ($typeHandlersTags as $typeHandlerTags) {
                $urlResolver->addMethodCall('addTypeHandler', array(new Reference($id), $typeHandlerTags['type']));
            }
        }
    }
}
